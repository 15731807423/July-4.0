<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;
use Translate\Controllers\DirectController;
use Translate\Controllers\Concerns\ValidatesTranslationInput;
use Translate\Controllers\TaskController;

class TranslateControllerValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('lang.multiple', true);
        config()->set('lang.translate', 'en');
        config()->set('lang.available', [
            'en' => ['translatable' => true],
            'de' => ['translatable' => true],
        ]);
    }

    public function test_task_result_rejects_invalid_state_without_calling_a_missing_method(): void
    {
        $response = (new TaskController())->batchResult(Request::create('/', 'POST', [
            'data' => 'not-json',
            'nodes' => [],
        ]));

        $this->assertFalse($response->getData(true)['status']);
        $this->assertSame('参数有误', $response->getData(true)['message']);
    }

    public function test_missing_controller_inputs_return_a_normal_error_response(): void
    {
        $direct = new DirectController();
        $task = new TaskController();

        $this->assertFalse($direct->batch(Request::create('/', 'POST'))->getData(true)['status']);
        $this->assertFalse($direct->page(Request::create('/', 'POST'))->getData(true)['status']);
        $this->assertFalse($task->tpl(Request::create('/', 'POST'))->getData(true)['status']);
    }

    public function test_page_with_only_excluded_fields_returns_an_error_instead_of_a_type_error(): void
    {
        config()->set('translate.fields', '["url"]');

        $response = (new DirectController())->page(Request::create('/', 'POST', [
            'code' => 'de',
            'text' => json_encode(['url' => '/example.html']),
        ]));

        $this->assertFalse($response->getData(true)['status']);
        $this->assertSame('没有要翻译的内容', $response->getData(true)['message']);
    }

    public function test_nested_page_values_and_list_task_payloads_are_rejected(): void
    {
        $validator = new class {
            use ValidatesTranslationInput;

            public function text(Request $request): ?array
            {
                return $this->translationText($request);
            }

            public function taskData(Request $request): ?string
            {
                return $this->translationTaskData($request);
            }

            public function nodeIds(Request $request): ?array
            {
                return $this->translationNodeIds($request);
            }
        };

        $this->assertNull($validator->text(Request::create('/', 'POST', [
            'text' => json_encode(['title' => ['nested' => 'value']]),
        ])));
        $this->assertSame(
            ['title' => 'Example'],
            $validator->text(Request::create('/', 'POST', [
                'text' => json_encode(['title' => 'Example']),
            ]))
        );
        $this->assertNull($validator->taskData(Request::create('/', 'POST', [
            'data' => '[]',
        ])));
        $this->assertSame(
            '{"id":"task-token"}',
            $validator->taskData(Request::create('/', 'POST', [
                'data' => '{"id":"task-token"}',
            ]))
        );
        $this->assertNull($validator->nodeIds(Request::create('/', 'POST', [
            'nodes' => [true],
        ])));
        $this->assertSame([1, 2], $validator->nodeIds(Request::create('/', 'POST', [
            'nodes' => ['1', 2, 2],
        ])));
    }
}
