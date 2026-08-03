<?php

namespace Tests\Feature;

use Tests\TestCase;
use Translate\Authentication;

class TranslatePublicKeyTest extends TestCase
{
    public function testPublicKeyEndpointExposesOnlyAValidPublicKey(): void
    {
        $publishedPath = sys_get_temp_dir() . '/july-public-key-' . bin2hex(random_bytes(8));
        $originalPath = config('translate.public_key_path');
        config(['translate.public_key_path' => $publishedPath]);

        try {
            $response = $this->get('/.well-known/july-translate-key');
            $response->assertOk();
            $response->assertHeader('X-Content-Type-Options', 'nosniff');
            $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
            $this->assertNotFalse(openssl_pkey_get_public($response->getContent()));
            $this->assertStringNotContainsString('PRIVATE KEY', $response->getContent());
            $this->assertFileExists($publishedPath);
            $this->assertSame(trim($response->getContent()), trim((string) file_get_contents($publishedPath)));
        } finally {
            config(['translate.public_key_path' => $originalPath]);
            @unlink($publishedPath);
        }
    }

    public function testPrivateKeyRootCanBeConfiguredOutsideTheWebRoot(): void
    {
        $configuredRoot = sys_get_temp_dir() . '/july-private-root-' . bin2hex(random_bytes(8));
        $originalRoot = config('translate.private_root');
        config(['translate.private_root' => $configuredRoot]);

        try {
            $this->assertSame($configuredRoot, Authentication::privateRootPath());
        } finally {
            config(['translate.private_root' => $originalRoot]);
        }
    }

    public function testBrowserEnrollmentEndpointAllowsCrossOriginPublicKeyRead(): void
    {
        $publishedPath = sys_get_temp_dir() . '/july-browser-public-key-' . bin2hex(random_bytes(8));
        $originalPath = config('translate.public_key_path');
        config(['translate.public_key_path' => $publishedPath]);

        try {
            $response = $this->get('/.well-known/july-translate-key-browser');

            $response->assertOk();
            $response->assertHeader('Access-Control-Allow-Origin', '*');
            $response->assertHeader('X-Content-Type-Options', 'nosniff');
            $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
            $this->assertNotFalse(openssl_pkey_get_public($response->getContent()));
            $this->assertStringNotContainsString('PRIVATE KEY', $response->getContent());
        } finally {
            config(['translate.public_key_path' => $originalPath]);
            @unlink($publishedPath);
        }
    }
}
