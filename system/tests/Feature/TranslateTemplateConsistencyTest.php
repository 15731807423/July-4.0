<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use ReflectionMethod;
use ReflectionProperty;
use Tests\Fixtures\FailingTemplateTranslate;
use Tests\TestCase;
use Translate\Translate;

class TranslateTemplateConsistencyTest extends TestCase
{
    private string $templateRoot;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.key', 'base64:' . base64_encode(str_repeat('t', 32)));
        config()->set('lang.translate', 'en');
        config()->set('lang.frontend', 'en');
        config()->set('lang.available', [
            'en' => ['translatable' => true],
            'de' => ['translatable' => true],
        ]);
        config()->set('translate.fields', '[]');
        config()->set('translate.text', '[]');
        config()->set('translate.replace', '[]');
        config()->set('translate.tool', 'alibabacloud');
        config()->set('translate.code', '{"alibabacloud":{"en":"en","de":"de"}}');

        $this->templateRoot = storage_path('framework/testing/translate-template-'
            . bin2hex(random_bytes(8))) . '/';
        File::ensureDirectoryExists($this->templateRoot . 'message/form');
        File::ensureDirectoryExists($this->templateRoot . 'message/content');
        $this->putTemplate('_layout.twig', 'Source layout');
        $this->putTemplate('new.twig', 'Source page');
        $this->putTemplate('message/form/contact.twig', 'Source form');
        $this->putTemplate('message/content/notice.twig', 'Source mail copy');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->templateRoot);
        foreach (glob(rtrim($this->templateRoot, '/') . '.translate-*') ?: [] as $path) {
            File::deleteDirectory($path);
        }

        parent::tearDown();
    }

    public function testExistingTemplatesArePreservedAndMissingFilesAreAdded(): void
    {
        $this->putTemplate('de/_layout.twig', 'Manual translation');
        $translate = $this->translate();
        $this->invokePrivate($translate, 'tplBefore');

        $snapshot = $this->cache($translate)['tplSnapshot'];
        $translated = $this->translatedPayload($snapshot['files']);

        $this->assertTrue($this->invokePrivate($translate, 'tplAfter', $translated));
        $this->assertSame('Manual translation', $this->readTemplate('de/_layout.twig'));
        $this->assertSame('Translated new.twig', $this->readTemplate('de/new.twig'));
        $this->assertSame(
            'Translated message/form/contact.twig',
            $this->readTemplate('de/message/form/contact.twig')
        );
        $this->assertSame('Source mail copy', $this->readTemplate('de/message/content/notice.twig'));
        $this->assertSame([], glob(rtrim($this->templateRoot, '/') . '.translate-*') ?: []);
    }

    public function testSourceChangesAbortWithoutPublishingPartialTemplates(): void
    {
        $this->putTemplate('de/_layout.twig', 'Manual translation');
        $translate = $this->translate();
        $this->invokePrivate($translate, 'tplBefore');
        $snapshot = $this->cache($translate)['tplSnapshot'];
        $this->putTemplate('new.twig', 'Updated source page');

        $this->assertFalse($this->invokePrivate(
            $translate,
            'tplAfter',
            $this->translatedPayload($snapshot['files'])
        ));
        $this->assertSame(
            '翻译期间源模板已更新，请重新翻译',
            $this->privateValue($translate, 'processingError')
        );
        $this->assertFileDoesNotExist($this->templateRoot . 'de/new.twig');
        $this->assertFileDoesNotExist($this->templateRoot . 'de/message/form/contact.twig');
        $this->assertSame('Manual translation', $this->readTemplate('de/_layout.twig'));
    }

    public function testCommitFailureRollsBackFilesAddedToAnExistingDirectory(): void
    {
        $this->putTemplate('de/_layout.twig', 'Manual translation');
        $translate = $this->translate(FailingTemplateTranslate::class);
        $this->invokePrivate($translate, 'tplBefore');
        $snapshot = $this->cache($translate)['tplSnapshot'];

        $this->assertFalse($this->invokePrivate(
            $translate,
            'tplAfter',
            $this->translatedPayload($snapshot['files'])
        ));
        $this->assertFileDoesNotExist($this->templateRoot . 'de/new.twig');
        $this->assertFileDoesNotExist($this->templateRoot . 'de/message/form/contact.twig');
        $this->assertSame('Manual translation', $this->readTemplate('de/_layout.twig'));
    }

    public function testSignedTaskDataRestoresTemplateSnapshotAcrossRequests(): void
    {
        $creator = $this->translate();
        $this->invokePrivate($creator, 'tplBefore');
        $snapshot = $this->cache($creator)['tplSnapshot'];
        $taskData = $this->invokePrivate($creator, 'encodeTaskData', '{"task_id":"relay"}');

        $resultRequest = $this->translate();
        $this->assertSame(
            '{"task_id":"relay"}',
            $this->invokePrivate($resultRequest, 'decodeTaskData', $taskData)
        );
        $this->assertTrue($this->invokePrivate(
            $resultRequest,
            'tplAfter',
            $this->translatedPayload($snapshot['files'])
        ));
        $this->assertSame('Translated new.twig', $this->readTemplate('de/new.twig'));
    }

    private function translate(string $class = Translate::class): Translate
    {
        $translate = (new $class(false))->setTo('de');
        (new ReflectionProperty(Translate::class, 'tplPath'))->setValue(
            $translate,
            $this->templateRoot
        );

        return $translate;
    }

    private function translatedPayload(array $files): string
    {
        return implode(
            '<div class="translate-field-cutting"></div>',
            array_map(fn (array $file): string => 'Translated ' . $file['path'], $files)
        );
    }

    private function putTemplate(string $relative, string $content): void
    {
        File::ensureDirectoryExists(dirname($this->templateRoot . $relative));
        file_put_contents($this->templateRoot . $relative, $content);
    }

    private function readTemplate(string $relative): string
    {
        return (string) file_get_contents($this->templateRoot . $relative);
    }

    private function cache(Translate $translate): array
    {
        return $this->privateValue($translate, 'cache');
    }

    private function privateValue(Translate $translate, string $property): mixed
    {
        return (new ReflectionProperty(Translate::class, $property))->getValue($translate);
    }

    private function invokePrivate(object $object, string $method, mixed ...$arguments): mixed
    {
        return (new ReflectionMethod(Translate::class, $method))->invokeArgs($object, $arguments);
    }
}
