<?php

namespace Installer;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class Installer
{
    /**
     * 检查安装环境
     *
     * @return array
     */
    public static function checkRequirements()
    {
        $results = [
            'PHP Version >= 8.1.0' => defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 80100
        ];

        foreach ([
            'BCMath',
            'Ctype',
            'JSON',
            'Tokenizer',
            'XML',
            'Fileinfo',
            'Mbstring',
            'OpenSSL',
            'PDO_SQLite',
            'PDO_MySQL'
        ] as $requirement) {
            $results[$requirement] = extension_loaded($requirement);
        }

        return $results;
    }

    /**
     * 创建 SQLite 数据库文件
     *
     * @param  string $database
     * @return void
     */
    public static function prepareDatabaseSqlite(string $database)
    {
        if (! is_file($database = database_path($database))) {
            touch($database);
        }
    }

    /**
     * 创建 MySQL 数据库
     *
     * @param  string $database
     * @return void
     */
    public static function prepareDatabaseMysql(string $database, string $username, string $password)
    {
        try {
            $connect = new \PDO('mysql:host=' . config('database.connections.mysql.host'), $username, $password);
            $connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            // create database {数据库名称} CHARACTER SET {字符集} COLLATE {排序规则}
            $connect->exec('CREATE DATABASE IF NOT EXISTS `' . $database . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        } catch (PDOException $e) {
        }

        unset($connect);
    }

    /**
     * 更新 .env 文件
     *
     * @param  array $settings
     * @return void
     */
    public static function updateEnv(array $settings)
    {
        Storage::disk('system')->put('.env', static::generateEnv($settings));
    }

    /**
     * 执行数据库迁移
     *
     * @return void
     */
    public static function migrate()
    {
        Artisan::call('migrate', [
            '--seed' => true,
            '--force' => true,
        ]);

        Storage::disk('system')->append('.env', "APP_INSTALLED=true\n");
        Artisan::call('cache:clear');
    }

    /**
     * 生成 .env 文件内容
     *
     * @param  array $settings
     * @return string
     */
    protected static function generateEnv($settings)
    {
        switch ($settings['type']) {
            case 'sqlite':
                return implode("\n", [
                    'APP_ENV='.config('app.env'),
                    'APP_DEBUG='.(config('app.debug') ? 'true' : 'false'),
                    'APP_KEY='.static::generateRandomKey(),
                    'APP_URL='.($settings['app_url'] ?? null),
                    'SITE_SUBJECT='.'"'.($settings['site_subject'] ?? null).'"',
                    'MAIL_TO_ADDRESS='.($settings['mail_to_address'] ?? null),
                    'MAIL_TO_NAME='.preg_replace('/@.*$/', '', ($settings['mail_to_address'] ?? null)),
                    '',
                    'DB_CONNECTION=sqlite',
                    'DB_SQLITE_DATABASE='.($settings['db_database'] ?? null),
                    '',
                ]);
                break;

            case 'mysql':
                return implode("\n", [
                    'APP_ENV='.config('app.env'),
                    'APP_DEBUG='.(config('app.debug') ? 'true' : 'false'),
                    'APP_KEY='.static::generateRandomKey(),
                    'APP_URL='.($settings['app_url'] ?? null),
                    'SITE_SUBJECT='.'"'.($settings['site_subject'] ?? null).'"',
                    'MAIL_TO_ADDRESS='.($settings['mail_to_address'] ?? null),
                    'MAIL_TO_NAME='.preg_replace('/@.*$/', '', ($settings['mail_to_address'] ?? null)),
                    '',
                    'DB_CONNECTION=mysql',
                    'DB_MYSQL_USERNAME='.($settings['db_username'] ?? null),
                    'DB_MYSQL_PASSWORD='.($settings['db_password'] ?? null),
                    'DB_MYSQL_DATABASE='.($settings['db_database'] ?? null),
                    '',
                ]);
                break;
        }
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected static function generateRandomKey()
    {
        return 'base64:'.base64_encode(
            Encrypter::generateKey(config('app.cipher'))
        );
    }
}
