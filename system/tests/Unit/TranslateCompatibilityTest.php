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
}
