<?php

namespace Tests\Feature;

use Tests\TestCase;

class TranslatePublicKeyTest extends TestCase
{
    public function test_public_key_endpoint_exposes_only_a_valid_public_key(): void
    {
        $response = $this->get('/.well-known/july-translate-key');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertNotFalse(openssl_pkey_get_public($response->getContent()));
        $this->assertStringNotContainsString('PRIVATE KEY', $response->getContent());
    }
}
