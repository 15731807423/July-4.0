<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Translate\Translate;

class TranslateResponseTest extends TestCase
{
    public function test_invalid_response_shapes_are_normalized_to_an_error(): void
    {
        foreach (['not-json', '{}', '[]', '{"status":true}', '{"status":"true","data":"ok"}'] as $response) {
            $decoded = Translate::decodeResponse($response);

            $this->assertFalse($decoded['status']);
            $this->assertSame('翻译接口返回格式不正确', $decoded['message']);
        }
    }

    public function test_valid_running_response_keeps_the_polling_contract(): void
    {
        $decoded = Translate::decodeResponse(json_encode([
            'status' => null,
            'message' => 'translating',
            'data' => '{"task_id":"example"}',
        ]));

        $this->assertNull($decoded['status']);
        $this->assertSame('translating', $decoded['message']);
        $this->assertSame('{"task_id":"example"}', $decoded['data']);
    }
}
