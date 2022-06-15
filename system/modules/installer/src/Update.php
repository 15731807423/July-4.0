<?php

namespace Installer;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class Update
{
    /**
     * 修改数据库链接
     * @param  array $database 数据库配置信息
     */
    public static function dbUpdate(array $database)
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');

        switch ($database['model']) {
            case 'mysql':
                if (config('database.default') == 'mysql') return response([]);

                if (!extension_loaded('PDO_MySQL')) {
                    return response('PDO_MySQL没有开启');
                }

                // 记录旧数据库
                $old = config('database.default');

                // 先确认有没有数据库文件，因为可能不是第一次切换。如果有，删除。清空全部表也可以，但是直接删文件更彻底
                try {
                    $connect = new \PDO('mysql:host=' . config('database.connections.mysql.host'), $database['mysql']['username'], $database['mysql']['password']);
                    $connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    // create database {数据库名称} CHARACTER SET {字符集} COLLATE {排序规则}
                    $connect->exec('CREATE DATABASE IF NOT EXISTS ' . $database['mysql']['database'] . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
                } catch (PDOException $e) {
                    return response('MySQL数据库创建失败');
                }

                unset($connect);

                // config切换数据库并设置数据库文件名
                config(['database.connections.mysql.username' => $database['mysql']['username']]);
                config(['database.connections.mysql.password' => $database['mysql']['password']]);
                config(['database.connections.mysql.database' => $database['mysql']['database']]);
                config(['database.default' => 'mysql']);
                static::env(['DB_MYSQL_USERNAME' => $database['mysql']['username']]);
                static::env(['DB_MYSQL_PASSWORD' => $database['mysql']['password']]);
                static::env(['DB_MYSQL_DATABASE' => $database['mysql']['database']]);
                static::env(['DB_CONNECTION' => 'mysql']);

                self::dropAll('mysql');

                // 执行迁移文件创建表
                $time = time();
                Artisan::call('migrate', ['--force' => true, '--seed' => false]);

                // 删除本次创建的迁移文件
                $dir = base_path('database/custom');
                $files = scandir($dir);
                foreach ($files as $key => $value) {
                    $file_time = strtotime(substr($value, 0, 4) . '-' . substr($value, 5, 2) . '-' . substr($value, 8, 2) . ' ' . substr($value, 11, 2) . ':' . substr($value, 13, 2) . ':' . substr($value, 15, 2));
                    if ($file_time > $time) {
                        unlink($dir . '/' . $value);
                    }
                }

                // 找出没有表的迁移文件，也就是后台手动创建的表，执行迁移文件
                $dir = base_path('database/custom');
                $files = scandir($dir);
                foreach ($files as $key => $value) {
                    if ($value == '.' || $value == '..') continue;

                    $table = substr($value, 18, -4);
                    if (Schema::hasTable($table)) continue;

                    rename($dir . '/' . $value, $dir . '/' . str_replace('.txt', '.php', $value));

                    Artisan::call('migrate', [
                        '--seed' => false,
                        '--force' => true,
                        '--path' => 'database/custom/' . str_replace('.txt', '.php', $value)
                    ]);

                    rename($dir . '/' . str_replace('.txt', '.php', $value), $dir . '/' . $value);
                }

                // 前面执行迁移文件时都没有使用填充数据 但是catalogs表里仍然有数据 暂时没有找到原因 先手动删除
                Db::connection('mysql')->delete('DELETE FROM catalogs');

                self::insertAll(self::dataAll($old), 'mysql');
                break;

            case 'sqlite':
                if (config('database.default') == 'sqlite') return response([]);

                if (!extension_loaded('PDO_SQLite')) {
                    return response('PDO_SQLite没有开启');
                }

                // 记录旧数据库
                $old = config('database.default');

                // 先确认有没有数据库文件，因为可能不是第一次切换。如果有，删除。清空全部表也可以，但是直接删文件更彻底
                if (is_file($file = database_path($database['sqlite']['database'] . '.db3'))) {
                    try {
                        unlink($file);
                    } catch (\Throwable $e) {
                        return response('文件正在使用');
                    }
                }

                // 创建数据库文件
                touch($file);

                // config切换数据库并设置数据库文件名
                config(['database.connections.sqlite.database' => database_path($database['sqlite']['database'] . '.db3')]);
                config(['database.default' => 'sqlite']);
                static::env(['DB_SQLITE_DATABASE' => $database['sqlite']['database'] . '.db3']);
                static::env(['DB_CONNECTION' => 'sqlite']);

                // 执行迁移文件创建表
                $time = time();
                Artisan::call('migrate', ['--force' => true, '--seed' => false]);

                // 删除本次创建的迁移文件
                $dir = base_path('database/custom');
                $files = scandir($dir);
                foreach ($files as $key => $value) {
                    $file_time = strtotime(substr($value, 0, 4) . '-' . substr($value, 5, 2) . '-' . substr($value, 8, 2) . ' ' . substr($value, 11, 2) . ':' . substr($value, 13, 2) . ':' . substr($value, 15, 2));
                    if ($file_time > $time) {
                        unlink($dir . '/' . $value);
                    }
                }

                // 找出没有表的迁移文件，也就是后台手动创建的表，执行迁移文件
                $dir = base_path('database/custom');
                $files = scandir($dir);
                foreach ($files as $key => $value) {
                    if ($value == '.' || $value == '..') continue;

                    $table = substr($value, 18, -4);
                    if (Schema::hasTable($table)) continue;

                    rename($dir . '/' . $value, $dir . '/' . str_replace('.txt', '.php', $value));

                    Artisan::call('migrate', [
                        '--seed' => false,
                        '--force' => true,
                        '--path' => 'database/custom/' . str_replace('.txt', '.php', $value)
                    ]);

                    rename($dir . '/' . str_replace('.txt', '.php', $value), $dir . '/' . $value);
                }

                // 前面执行迁移文件时都没有使用填充数据 但是catalogs表里仍然有数据 暂时没有找到原因 先手动删除
                Db::connection('sqlite')->delete('DELETE FROM catalogs');

                self::insertAll(self::dataAll($old), 'sqlite');
                break;
            
            default:
                return response('不支持的数据库类型');
                break;
        }

        Artisan::call('cache:clear');
        Artisan::call('view:clear');

        return response([]);
    }

    private static function dataAll(string $model)
    {
        switch ($model) {
            case 'mysql':
                $list = [];
                $db = Db::connection('mysql');
                foreach (self::tableAll('mysql') as $key => $value) {
                    $list[$value] = $db->select('SELECT * FROM ' . $value);
                }
                foreach ($list as $table => $datas) {
                    foreach ($datas as $key => $data) {
                        $list[$table][$key] = json_decode(json_encode($data), true);
                    }
                }
                unset($list['migrations']);
                return $list;
                break;

            case 'sqlite':
                $list = [];
                $db = Db::connection('sqlite');
                foreach (self::tableAll('sqlite') as $key => $value) {
                    $list[$value] = $db->select('SELECT * FROM ' . $value);
                }
                foreach ($list as $table => $datas) {
                    foreach ($datas as $key => $data) {
                        $list[$table][$key] = json_decode(json_encode($data), true);
                    }
                }
                unset($list['migrations']);
                return $list;
                break;

            default:
                return [];
                break;
        }
    }

    private static function dropAll(string $model)
    {
        switch ($model) {
            case 'mysql':
                $db = Db::connection('mysql');
                foreach (self::tableAll('mysql') as $key => $value) {
                    $db->statement('DROP TABLE ' . $value);
                }
                break;

            case 'sqlite':
                $db = Db::connection('sqlite');
                foreach (self::tableAll('sqlite') as $key => $value) {
                    $db->statement('DROP TABLE ' . $value);
                }
                $db->statement('TRUNCATE TABLE sqlite_sequence');
                break;
            
            default:
                // code...
                break;
        }
    }

    private static function tableAll(string $model)
    {
        switch ($model) {
            case 'mysql':
                $list = [];
                $db = Db::connection('mysql');
                $tables = $db->select('SHOW TABLES');
                foreach ($tables as $key => $value) {
                    $list[] = $value->{'Tables_in_' . config('db.mysql.database')};
                }
                return $list;
                break;

            case 'sqlite':
                $list = [];
                $db = Db::connection('sqlite');
                $tables = $db->select('SELECT name FROM sqlite_master WHERE type = \'table\' AND name != \'sqlite_sequence\'');
                foreach ($tables as $key => $value) {
                    $list[] = $value->name;
                }
                return $list;
                break;
            
            default:
                return [];
                break;
        }
    }

    private static function insertAll(array $list, string $model)
    {
        $loop = ['messages', 'node__content'];
        foreach ($list as $table => $all) {
            if (in_array($table, $loop)) {
                foreach ($all as $index => $data) {
                    Db::connection($model)->table($table)->insert($data);
                }
            } else {
                $count = intval(ceil(count($all) / 50));
                for ($i = 0; $i < $count; $i++) { 
                    Db::connection($model)->table($table)->insert(array_splice($all, 0, 50));
                }
            }
        }
    }

    private static function env(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_null(env($key))) {
                Storage::disk('system')->append('.env', $key . '=' . $value . "\n");
            }
        }

        $path = base_path() . DIRECTORY_SEPARATOR . '.env';
        $env = collect(file($path, FILE_IGNORE_NEW_LINES));
        $env->transform(function ($item) use ($data) {
            foreach ($data as $key => $value) {
                if (strpos($item, $key . '=') === 0) {
                    return $key . '=' . $value;
                }
            }
            return $item;
        });
        $content = implode("\n", $env->toArray());
        Storage::disk('system')->put('.env', $content);
    }
}
