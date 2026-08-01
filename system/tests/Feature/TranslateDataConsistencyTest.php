<?php

namespace Tests\Feature;

use App\EntityField\FieldTypes\Html;
use App\EntityField\FieldTypes\Image;
use App\EntityField\FieldTypes\Timeout;
use App\EntityField\FieldTypes\Url;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use ReflectionProperty;
use Tests\TestCase;
use Translate\Translate;

class TranslateDataConsistencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.key', 'base64:' . base64_encode(str_repeat('k', 32)));
        config()->set('lang.frontend', 'en');
        config()->set('lang.translate', 'en');
        config()->set('lang.available', [
            'en' => ['translatable' => true],
            'de' => ['translatable' => true],
        ]);
        config()->set('translate.fields', '[]');
        config()->set('translate.text', '[]');
        config()->set('translate.replace', '[]');
        config()->set('translate.tool', 'alibabacloud');
        config()->set('translate.code', json_encode([
            'alibabacloud' => ['en' => 'en', 'de' => 'de'],
        ]));

        Schema::dropIfExists('node__body');
        Schema::dropIfExists('node_fields');
        Schema::dropIfExists('node_translations');
        Schema::dropIfExists('nodes');

        Schema::create('nodes', function (Blueprint $table): void {
            $table->id();
            $table->string('mold_id');
            $table->string('title');
            $table->string('view')->nullable();
            $table->boolean('is_red')->default(false);
            $table->boolean('is_green')->default(false);
            $table->boolean('is_blue')->default(false);
            $table->string('langcode', 12);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('node_translations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('entity_id');
            $table->string('mold_id');
            $table->string('title');
            $table->string('view')->nullable();
            $table->boolean('is_red')->default(false);
            $table->boolean('is_green')->default(false);
            $table->boolean('is_blue')->default(false);
            $table->string('langcode', 12);
            $table->timestamps();
        });

        Schema::create('node_fields', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('field_type');
        });

        $this->createBodyTable();

        DB::table('nodes')->insert([
            'id' => 1,
            'mold_id' => 'page',
            'title' => 'Source title',
            'view' => 'page.twig',
            'is_red' => false,
            'is_green' => false,
            'is_blue' => false,
            'langcode' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('node_fields')->insert(['id' => 'body', 'field_type' => Html::class]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('node__body');
        Schema::dropIfExists('node_fields');
        Schema::dropIfExists('node_translations');
        Schema::dropIfExists('nodes');

        parent::tearDown();
    }

    public function testStringZeroIsKeptAsTranslatableContent(): void
    {
        $this->putSourceBody('0');

        $content = $this->invokePrivate($this->translate(), 'getPageContent', 1, 'de');

        $this->assertSame([
            'title' => 'Source title',
            'body' => '0',
        ], $content);
    }

    public function testExistingTitleTranslationDoesNotHideOtherMissingFields(): void
    {
        $this->putSourceBody('Source body');
        $this->putManualTitle('Manueller Titel');

        $content = $this->invokePrivate($this->translate(), 'getPageContent', 1, 'de');

        $this->assertSame(['body' => 'Source body'], $content);
    }

    public function testLinksImagesAndSchedulingFieldsAreNeverSentForTranslation(): void
    {
        $this->putSourceBody('Source body');
        DB::table('node_fields')->insert([
            ['id' => 'canonical', 'field_type' => Url::class],
            ['id' => 'image', 'field_type' => Image::class],
            ['id' => 'publish_at', 'field_type' => Timeout::class],
        ]);

        $content = $this->invokePrivate($this->translate(), 'getPageContent', 1, 'de');

        $this->assertSame([
            'title' => 'Source title',
            'body' => 'Source body',
        ], $content);
    }

    public function testBatchResultRejectsAPageWithTheWrongFieldCountBeforeWriting(): void
    {
        $this->putSourceBody('Source body');
        $this->putNode(2, 'Second source title');
        $this->putSourceBody('Second source body', 2);

        $result = $this->invokePrivate(
            $this->translate(true, [1, 2]),
            'batchAfter',
            'Titel<div class="translate-field-cutting"></div>Inhalt'
                . '<div class="translate-page-cutting"></div>Nur ein Feld'
        );

        $this->assertSame('翻译前后字段数量不一致', $result);
        $this->assertSame(0, DB::table('node_translations')->count());
        $this->assertSame(0, DB::table('node__body')->where('langcode', 'de')->count());
    }

    public function testBatchWriteRollsBackEveryTableWhenALaterFieldFails(): void
    {
        Schema::drop('node__body');
        $this->createBodyTable(true);
        $this->putSourceBody('Source body');

        $result = $this->invokePrivate(
            $this->translate(),
            'batchAfter',
            'Titel<div class="translate-field-cutting"></div>Inhalt'
        );

        $this->assertSame('翻译结果写入失败', $result);
        $this->assertSame(0, DB::table('node_translations')->count());
        $this->assertSame(0, DB::table('node__body')->where('langcode', 'de')->count());
    }

    public function testInvalidBatchShapeIsReturnedAsAnErrorResponse(): void
    {
        $this->putSourceBody('Source body');
        $translate = $this->translate();
        $this->setPrivate($translate, 'result', 'Nur ein Feld');

        $response = $this->invokePrivate($translate, 'end', 'batch', true)->getData(true);

        $this->assertFalse($response['status']);
        $this->assertSame('翻译前后字段数量不一致', $response['message']);
        $this->assertSame(0, DB::table('node_translations')->count());
    }

    public function testBatchUsesTheOriginalFieldOrderAndPreservesANewManualTranslation(): void
    {
        $this->putSourceBody('Source body');
        $translate = $this->translate();

        $this->invokePrivate($translate, 'batchBefore');
        $this->putManualTitle('Manueller Titel');

        $result = $this->invokePrivate(
            $translate,
            'batchAfter',
            'Maschineller Titel<div class="translate-field-cutting"></div>Translated body'
        );

        $this->assertSame('翻译成功', $result);
        $this->assertSame('Manueller Titel', DB::table('node_translations')->value('title'));
        $this->assertSame(
            'Translated body',
            DB::table('node__body')->where('langcode', 'de')->value('body')
        );
    }

    public function testSignedTaskDataRestoresTheSnapshotInALaterRequest(): void
    {
        $this->putSourceBody('Source body');
        $creator = $this->translate(false);
        $this->invokePrivate($creator, 'batchBefore');

        $taskData = $this->invokePrivate($creator, 'encodeBatchTaskData', '{"id":"relay-task"}');

        $pollRequest = $this->translate(false);
        $relayData = $this->invokePrivate($pollRequest, 'decodeBatchTaskData', $taskData);
        $nextTaskData = $this->invokePrivate($pollRequest, 'encodeBatchTaskData', '{"id":"relay-task-2"}');

        $resultRequest = $this->translate(false);
        $nextRelayData = $this->invokePrivate($resultRequest, 'decodeBatchTaskData', $nextTaskData);
        $this->putManualTitle('Manueller Titel');
        $result = $this->invokePrivate(
            $resultRequest,
            'batchAfter',
            'Maschineller Titel<div class="translate-field-cutting"></div>Translated body'
        );

        $this->assertSame('{"id":"relay-task"}', $relayData);
        $this->assertSame('{"id":"relay-task-2"}', $nextRelayData);
        $this->assertSame('翻译成功', $result);
        $this->assertSame('Manueller Titel', DB::table('node_translations')->value('title'));
        $this->assertSame(
            'Translated body',
            DB::table('node__body')->where('langcode', 'de')->value('body')
        );
    }

    public function testLegacyTaskDataRemainsCompatibleAndTamperedSnapshotsAreRejected(): void
    {
        $this->putSourceBody('Source body');
        $creator = $this->translate(false);
        $this->invokePrivate($creator, 'batchBefore');
        $taskData = $this->invokePrivate($creator, 'encodeBatchTaskData', '{"id":"relay-task"}');

        $envelope = json_decode($taskData, true);
        $envelope['signature'][0] = $envelope['signature'][0] === 'a' ? 'b' : 'a';
        $tampered = json_encode($envelope);

        $this->assertNull($this->invokePrivate($this->translate(false), 'decodeBatchTaskData', $tampered));
        $this->assertSame(
            '{"id":"legacy-task"}',
            $this->invokePrivate($this->translate(false), 'decodeBatchTaskData', '{"id":"legacy-task"}')
        );
    }

    public function testDomParserPreservesUtf8EmojiAndHtmlWithoutDeprecation(): void
    {
        $deprecations = [];
        set_error_handler(function (int $severity, string $message) use (&$deprecations): bool {
            if ($severity === E_DEPRECATED) {
                $deprecations[] = $message;
                return true;
            }

            return false;
        });

        try {
            $dom = $this->invokePrivate(
                $this->translate(),
                'DOMDocument',
                '<p>中文 😀 &amp; HTML</p><a href="/example.html">链接</a>'
            );
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $deprecations);
        $this->assertSame('中文 😀 & HTML', $dom->getElementsByTagName('p')->item(0)->textContent);
        $this->assertSame('链接', $dom->getElementsByTagName('a')->item(0)->textContent);
        $this->assertSame('/example.html', $dom->getElementsByTagName('a')->item(0)->getAttribute('href'));
    }

    private function translate(bool $direct = true, array $nodes = [1]): Translate
    {
        return (new Translate($direct))->setTo('de')->setNodes($nodes);
    }

    private function createBodyTable(bool $uniqueEntity = false): void
    {
        Schema::create('node__body', function (Blueprint $table) use ($uniqueEntity): void {
            $table->id();
            $entity = $table->unsignedBigInteger('entity_id');
            if ($uniqueEntity) {
                $entity->unique();
            }
            $table->text('body')->nullable();
            $table->string('langcode', 12);
            $table->timestamps();
        });
    }

    private function putSourceBody(string $body, int $id = 1): void
    {
        DB::table('node__body')->insert([
            'entity_id' => $id,
            'body' => $body,
            'langcode' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function putNode(int $id, string $title): void
    {
        DB::table('nodes')->insert([
            'id' => $id,
            'mold_id' => 'page',
            'title' => $title,
            'view' => 'page.twig',
            'is_red' => false,
            'is_green' => false,
            'is_blue' => false,
            'langcode' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function putManualTitle(string $title): void
    {
        DB::table('node_translations')->insert([
            'entity_id' => 1,
            'mold_id' => 'page',
            'title' => $title,
            'view' => 'page.twig',
            'is_red' => false,
            'is_green' => false,
            'is_blue' => false,
            'langcode' => 'de',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function invokePrivate(object $object, string $method, mixed ...$arguments): mixed
    {
        $reflection = new ReflectionMethod($object, $method);

        return $reflection->invokeArgs($object, $arguments);
    }

    private function setPrivate(object $object, string $property, mixed $value): void
    {
        (new ReflectionProperty($object, $property))->setValue($object, $value);
    }
}
