<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Translate\Authentication;

class TranslateAuthenticationTest extends TestCase
{
    public function test_signature_payload_is_stable_and_binds_the_request_body(): void
    {
        $payload = Authentication::signaturePayload(
            'WWW.Example.COM',
            'post',
            '/api/v2/translate/create',
            1700000000,
            'ABCDEF0123456789ABCDEF0123456789',
            'html=hello&source=en&target=de'
        );

        $this->assertSame(implode("\n", [
            'www.example.com',
            'POST',
            '/api/v2/translate/create',
            '1700000000',
            'abcdef0123456789abcdef0123456789',
            hash('sha256', 'html=hello&source=en&target=de'),
        ]), $payload);

        $changed = Authentication::signaturePayload(
            'WWW.Example.COM',
            'post',
            '/api/v2/translate/create',
            1700000000,
            'ABCDEF0123456789ABCDEF0123456789',
            'html=changed&source=en&target=de'
        );

        $this->assertNotSame($payload, $changed);
    }

    public function test_key_pair_validation_rejects_a_mismatched_public_key(): void
    {
        $first = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        $second = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        $this->assertNotFalse($first);
        $this->assertNotFalse($second);
        openssl_pkey_export($first, $privateKey);
        $firstPublicKey = openssl_pkey_get_details($first)['key'];
        $secondPublicKey = openssl_pkey_get_details($second)['key'];

        $this->assertTrue(Authentication::keyPairMatches($privateKey, $firstPublicKey));
        $this->assertFalse(Authentication::keyPairMatches($privateKey, $secondPublicKey));
    }
}
