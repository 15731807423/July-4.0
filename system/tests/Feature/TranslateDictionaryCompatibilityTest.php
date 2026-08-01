<?php

namespace Tests\Feature;

use App\Http\Actions\Export;
use App\Support\Settings\Translate as TranslateSettings;
use July\Node\TwigExtensions\NodeQueryExtension;
use ReflectionMethod;
use ReflectionProperty;
use Tests\TestCase;
use Translate\Translate;

class TranslateDictionaryCompatibilityTest extends TestCase
{
    public function testExportKeepsRuntimeLanguageKeys(): void
    {
        $replace = '{"de":{"Company":"Unternehmen"}}';
        config()->set('lang.frontend', 'en');
        config()->set('translate.code', '{}');
        config()->set('translate.fields', '[]');
        config()->set('translate.text', '[]');
        config()->set('translate.replace', $replace);

        $export = new Export();
        (new ReflectionMethod($export, 'translate'))->invoke($export);
        $data = (new ReflectionProperty($export, 'data'))->getValue($export);

        $this->assertSame($replace, $data['translate']['replace']);
    }

    public function testLegacyCombinedKeysAreNormalizedWithoutOverwritingModernKeys(): void
    {
        $normalized = TranslateSettings::normalizeImportedReplacementJson(json_encode([
            'de' => ['Company' => 'Modern'],
            'en_to_de' => ['Company' => 'Legacy'],
            'en_to_fr' => ['Company' => 'Entreprise'],
        ]), 'en');

        $this->assertSame([
            'de' => ['Company' => 'Modern'],
            'fr' => ['Company' => 'Entreprise'],
        ], json_decode($normalized, true));
    }

    public function testTwigConfigUsesJsonDictionaryAndSupportsLegacyFrontendKey(): void
    {
        config()->set('example.company', 'Company');
        config()->set('lang.translate', 'cn');
        config()->set('lang.frontend', 'en');
        config()->set('translate.replace', json_encode([
            'en_to_de' => ['Company' => 'Unternehmen'],
        ]));

        $callable = $this->configFunction();

        $this->assertSame('Unternehmen', $callable('example.company', 'de'));
    }

    public function testTranslationRequestUsesLegacyCombinedDictionaryKey(): void
    {
        config()->set('lang.translate', 'cn');
        config()->set('lang.frontend', 'en');
        config()->set('lang.available', [
            'cn' => ['translatable' => true],
            'de' => ['translatable' => true],
        ]);
        config()->set('translate.fields', '[]');
        config()->set('translate.text', '[]');
        config()->set('translate.tool', 'alibabacloud');
        config()->set('translate.code', '{}');
        config()->set('translate.replace', json_encode([
            'en_to_de' => ['Company' => 'Unternehmen'],
        ]));

        $translate = (new Translate())->setTo('de');
        $map = (new ReflectionMethod($translate, 'getAppoint'))->invoke($translate);

        $this->assertSame(['Company' => 'Unternehmen'], $map);
    }

    public function testTwigConfigNeverExecutesInvalidJsonAsPhp(): void
    {
        config()->set('example.company', 'Company');
        config()->set('lang.translate', 'en');
        config()->set('lang.frontend', 'en');
        config()->set('translate.replace', 'throw new RuntimeException("executed")');

        $this->assertSame('Company', $this->configFunction()('example.company', 'de'));
    }

    private function configFunction(): callable
    {
        foreach ((new NodeQueryExtension())->getFunctions() as $function) {
            if ($function->getName() === 'config') {
                return $function->getCallable();
            }
        }

        $this->fail('Twig config function was not registered');
    }
}
