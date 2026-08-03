<?php

namespace Translate;

use RuntimeException;

class Authentication
{
    public static function keyPairMatches(string $privateKey, string $publicKey): bool
    {
        $private = openssl_pkey_get_private($privateKey);
        $public = openssl_pkey_get_public($publicKey);
        if ($private === false || $public === false) {
            return false;
        }

        $details = openssl_pkey_get_details($private);
        $derivedPublicKey = is_array($details) ? ($details['key'] ?? null) : null;

        return is_string($derivedPublicKey)
            && hash_equals(trim($derivedPublicKey), trim($publicKey));
    }

    public static function publicKey(): string
    {
        self::ensureKeyPair();
        $key = self::readPublicKey();
        self::publishPublicKeyFile($key);

        return $key;
    }

    public static function initialize(): void
    {
        self::ensureKeyPair();
        self::publishPublicKeyFile(self::readPublicKey());
    }

    public static function privateRootPath(): string
    {
        $configured = config('translate.private_root');
        if (is_string($configured) && trim($configured) !== '') {
            return rtrim(trim($configured), '\\/');
        }

        $home = $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'] ?? null;
        if (!is_string($home) || trim($home) === '') {
            $home = getenv('HOME') ?: getenv('USERPROFILE');
        }
        if (is_string($home) && trim($home) !== '') {
            return rtrim(trim($home), '\\/') . '/.july-private';
        }

        return dirname(base_path(), 3) . '/.july-private';
    }

    private static function readPublicKey(): string
    {
        $key = file_get_contents(self::publicKeyPath());
        if (!is_string($key) || openssl_pkey_get_public($key) === false) {
            throw new RuntimeException('翻译鉴权公钥读取失败');
        }

        return $key;
    }

    public static function post(string $url, array $data, string $site): string
    {
        $body = http_build_query($data);
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $timestamp = time();
        $nonce = bin2hex(random_bytes(16));
        $payload = self::signaturePayload($site, 'POST', $path, $timestamp, $nonce, $body);

        self::initialize();
        $privateKey = openssl_pkey_get_private((string) file_get_contents(self::privateKeyPath()));
        if ($privateKey === false || !openssl_sign($payload, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('翻译请求签名失败');
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'X-July-Site: ' . strtolower($site),
                'X-July-Timestamp: ' . $timestamp,
                'X-July-Nonce: ' . $nonce,
                'X-July-Signature: ' . base64_encode($signature),
            ],
        ]);

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);

        if (!is_string($response)) {
            throw new RuntimeException($error ?: '翻译中转站连接失败');
        }
        if ($status < 200 || $status >= 300) {
            $decoded = json_decode($response, true);
            $message = is_array($decoded) ? ($decoded['message'] ?? $response) : $response;
            throw new RuntimeException(is_string($message) && $message !== '' ? $message : '翻译中转站请求失败');
        }

        return $response;
    }

    public static function signaturePayload(
        string $site,
        string $method,
        string $path,
        int $timestamp,
        string $nonce,
        string $body
    ): string {
        return implode("\n", [
            strtolower($site),
            strtoupper($method),
            $path,
            (string) $timestamp,
            strtolower($nonce),
            hash('sha256', $body),
        ]);
    }

    private static function ensureKeyPair(): void
    {
        $directory = self::directory();
        $privateKeyPath = $directory . '/private.pem';
        $publicKeyPath = $directory . '/public.pem';
        $lock = fopen($directory . '/key-pair.lock', 'c+');
        if (!$lock || !flock($lock, LOCK_EX)) {
            if ($lock) {
                fclose($lock);
            }
            throw new RuntimeException('翻译鉴权密钥锁定失败');
        }

        try {
            if (is_file($privateKeyPath) && is_file($publicKeyPath)) {
                $storedPrivateKey = file_get_contents($privateKeyPath);
                $storedPublicKey = file_get_contents($publicKeyPath);
                if (
                    is_string($storedPrivateKey)
                    && is_string($storedPublicKey)
                    && self::keyPairMatches($storedPrivateKey, $storedPublicKey)
                ) {
                    return;
                }
            }

            $key = openssl_pkey_new([
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ]);
            if ($key === false || !openssl_pkey_export($key, $privateKey)) {
                throw new RuntimeException('翻译鉴权密钥生成失败');
            }

            $details = openssl_pkey_get_details($key);
            $publicKey = is_array($details) ? ($details['key'] ?? null) : null;
            if (!is_string($publicKey)) {
                throw new RuntimeException('翻译鉴权公钥生成失败');
            }

            self::atomicWrite($privateKeyPath, $privateKey);
            self::atomicWrite($publicKeyPath, $publicKey);
            @chmod($privateKeyPath, 0600);

            if (
                !self::keyPairMatches(
                    (string) file_get_contents($privateKeyPath),
                    (string) file_get_contents($publicKeyPath)
                )
            ) {
                throw new RuntimeException('翻译鉴权密钥校验失败');
            }
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private static function directory(): string
    {
        $projectRoot = dirname(base_path());
        $siteDirectory = substr(hash('sha256', realpath($projectRoot) ?: $projectRoot), 0, 24);
        $directory = self::privateRootPath() . '/' . $siteDirectory . '/translate-auth';
        $legacyDirectory = dirname($projectRoot) . '/.july-private/' . $siteDirectory . '/translate-auth';

        self::migrateLegacyDirectory($legacyDirectory, $directory);
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new RuntimeException('翻译鉴权目录创建失败');
        }

        return $directory;
    }

    private static function privateKeyPath(): string
    {
        return self::directory() . '/private.pem';
    }

    private static function publicKeyPath(): string
    {
        return self::directory() . '/public.pem';
    }

    private static function publishedPublicKeyPath(): string
    {
        $configured = config('translate.public_key_path');
        if (is_string($configured) && trim($configured) !== '') {
            return trim($configured);
        }

        return dirname(base_path()) . '/.well-known/july-translate-key';
    }

    private static function publishPublicKeyFile(string $key): bool
    {
        try {
            $path = self::publishedPublicKeyPath();
            $directory = dirname($path);
            if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
                return false;
            }

            $publishedKey = is_file($path) ? file_get_contents($path) : null;
            if (!is_string($publishedKey) || !hash_equals(trim($key), trim($publishedKey))) {
                self::atomicWrite($path, trim($key) . PHP_EOL);
                @chmod($path, 0644);
            }

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private static function migrateLegacyDirectory(string $legacy, string $target): void
    {
        if (self::samePath($legacy, $target) || !is_dir($legacy)) {
            return;
        }

        $parent = dirname($target);
        if (!is_dir($parent) && !mkdir($parent, 0700, true) && !is_dir($parent)) {
            throw new RuntimeException('翻译鉴权目录迁移失败');
        }

        $lock = fopen($parent . '/migration.lock', 'c+');
        if (!$lock || !flock($lock, LOCK_EX)) {
            if ($lock) {
                fclose($lock);
            }
            throw new RuntimeException('翻译鉴权目录迁移锁定失败');
        }

        try {
            if (!is_dir($legacy)) {
                return;
            }
            if (is_dir($target)) {
                $legacyPrivateKey = is_file($legacy . '/private.pem')
                    ? file_get_contents($legacy . '/private.pem')
                    : null;
                $legacyPublicKey = is_file($legacy . '/public.pem')
                    ? file_get_contents($legacy . '/public.pem')
                    : null;
                $targetPrivateKey = is_file($target . '/private.pem')
                    ? file_get_contents($target . '/private.pem')
                    : null;
                $targetPublicKey = is_file($target . '/public.pem')
                    ? file_get_contents($target . '/public.pem')
                    : null;
                if (
                    !is_string($legacyPrivateKey)
                    || !is_string($legacyPublicKey)
                    || !is_string($targetPrivateKey)
                    || !is_string($targetPublicKey)
                    || !self::keyPairMatches($legacyPrivateKey, $legacyPublicKey)
                    || !self::keyPairMatches($targetPrivateKey, $targetPublicKey)
                    || !hash_equals(trim($legacyPublicKey), trim($targetPublicKey))
                ) {
                    throw new RuntimeException('翻译鉴权密钥迁移冲突');
                }

                self::removeLegacyKeyMaterial($legacy);
                return;
            }
            if (@rename($legacy, $target)) {
                @chmod($target, 0700);
                @chmod($target . '/private.pem', 0600);
                return;
            }

            $privateKey = file_get_contents($legacy . '/private.pem');
            $publicKey = file_get_contents($legacy . '/public.pem');
            if (
                !is_string($privateKey)
                || !is_string($publicKey)
                || !self::keyPairMatches($privateKey, $publicKey)
            ) {
                throw new RuntimeException('旧翻译鉴权密钥校验失败');
            }
            if (!is_dir($target) && !mkdir($target, 0700, true) && !is_dir($target)) {
                throw new RuntimeException('翻译鉴权目录迁移失败');
            }

            self::atomicWrite($target . '/private.pem', $privateKey);
            self::atomicWrite($target . '/public.pem', $publicKey);
            @chmod($target . '/private.pem', 0600);
            if (
                !self::keyPairMatches(
                    (string) file_get_contents($target . '/private.pem'),
                    (string) file_get_contents($target . '/public.pem')
                )
            ) {
                throw new RuntimeException('翻译鉴权密钥迁移校验失败');
            }

            self::removeLegacyKeyMaterial($legacy);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private static function removeLegacyKeyMaterial(string $directory): void
    {
        if (is_file($directory . '/private.pem') && !@unlink($directory . '/private.pem')) {
            throw new RuntimeException('旧翻译鉴权私钥删除失败');
        }

        @unlink($directory . '/public.pem');
        @unlink($directory . '/key-pair.lock');
        @rmdir($directory);
    }

    private static function samePath(string $first, string $second): bool
    {
        $normalize = static function (string $path): string {
            $path = str_replace('\\', '/', rtrim($path, '\\/'));

            return DIRECTORY_SEPARATOR === '\\' ? strtolower($path) : $path;
        };

        return $normalize($first) === $normalize($second);
    }

    private static function atomicWrite(string $path, string $content): void
    {
        $temporary = $path . '.' . bin2hex(random_bytes(8)) . '.tmp';
        if (file_put_contents($temporary, $content, LOCK_EX) === false) {
            @unlink($temporary);
            throw new RuntimeException('翻译鉴权密钥保存失败');
        }
        if (@rename($temporary, $path)) {
            return;
        }

        $backup = $path . '.' . bin2hex(random_bytes(8)) . '.bak';
        if (
            !is_file($path)
            || !@rename($path, $backup)
            || !@rename($temporary, $path)
        ) {
            if (is_file($backup) && !is_file($path)) {
                @rename($backup, $path);
            }
            @unlink($temporary);
            throw new RuntimeException('翻译鉴权密钥保存失败');
        }

        @unlink($backup);
    }
}
