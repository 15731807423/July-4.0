<?php

namespace Tests\Unit;

use App\Support\Settings\Translate as TranslateSettings;
use PHPUnit\Framework\TestCase;
use Translate\Translate;

class TranslateCompatibilityTest extends TestCase
{
    public function test_batch_targets_include_every_translatable_language_except_the_source(): void
    {
        $available = [
            'en' => ['translatable' => true],
            'de' => ['translatable' => true],
            'fr' => ['translatable' => false],
            'es' => ['translatable' => true],
        ];

        $this->assertSame(['de', 'es'], Translate::batchTargetCodes($available, 'en'));
    }

    public function test_legacy_batch_request_uses_the_first_non_source_language(): void
    {
        $available = [
            'de' => ['translatable' => true],
            'en' => ['translatable' => true],
            'es' => ['translatable' => true],
        ];

        $this->assertSame('de', Translate::legacyBatchTargetCode($available, 'en'));
    }

    public function test_language_codes_are_mapped_in_both_directions(): void
    {
        $mapping = ['cn' => 'zh-Hant', 'en' => 'en'];

        $this->assertSame('zh-Hant', Translate::mapLanguageCode('cn', $mapping));
        $this->assertSame('cn', Translate::mapLanguageCode('zh-Hant', $mapping, false));
        $this->assertSame('de', Translate::mapLanguageCode('de', $mapping));
    }

    public function test_legacy_tool_values_are_normalized_to_alibaba_cloud(): void
    {
        $this->assertSame('alibabacloud', Translate::normalizeTool('alibabacloud'));
        $this->assertSame('alibabacloud', Translate::normalizeTool('tencentcloud'));
        $this->assertSame('alibabacloud', Translate::normalizeTool('azure'));
    }

    public function test_hidden_tool_setting_is_kept_when_settings_are_saved(): void
    {
        $this->assertArrayHasKey('translate.tool', (new TranslateSettings())->getItems());
    }

    public function test_current_and_legacy_json_setting_shapes_are_accepted(): void
    {
        $errors = TranslateSettings::jsonValidationErrors([
            'translate.code' => '{"alibabacloud":{"en":"en","de":"de"},"azure":{"de":"de"}}',
            'translate.fields' => '{"de":["url","image_src"]}',
            'translate.text' => '["Brand name"]',
            'translate.replace' => '{"de":[]}',
        ]);

        $this->assertSame([], $errors);
    }

    public function test_invalid_json_and_unsafe_setting_shapes_are_rejected(): void
    {
        $errors = TranslateSettings::jsonValidationErrors([
            'translate.code' => '{invalid',
            'translate.fields' => '{"de":"url"}',
            'translate.text' => '["valid",42]',
            'translate.replace' => '["not-a-map"]',
        ]);

        $this->assertSame([
            'translate.code',
            'translate.fields',
            'translate.text',
            'translate.replace',
        ], array_keys($errors));
    }
}
