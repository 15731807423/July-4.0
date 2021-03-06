<?php

use App\EntityField\FieldTypes\FieldTypeManager;
use App\Models\ModelBase;
use App\Support\Lang;
use App\Support\Types;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Specs\spec;
use Illuminate\Support\Facades\Artisan;

if (! function_exists('backend_path')) {
    /**
     * 后台主题路径
     *
     * @param  string  $path
     * @return string
     */
    function backend_path($path = '')
    {
        $path = 'themes'.DIRECTORY_SEPARATOR.'backend'.
            ($path ? DIRECTORY_SEPARATOR.ltrim($path, '\\/') : $path);

        return public_path($path);
    }
}

if (! function_exists('frontend_path')) {
    /**
     * 前端主题路径
     *
     * @param  string  $path
     * @return string
     */
    function frontend_path($path = '')
    {
        $path = 'themes'.DIRECTORY_SEPARATOR.'frontend'.
            ($path ? DIRECTORY_SEPARATOR.ltrim($path, '\\/') : $path);

        return public_path($path);
    }
}

if (! function_exists('base64_decode_array')) {
    /**
     * 递归解码 base64 编码过的数组
     *
     * @param array $data 待解码数组
     * @param array $except 指定未编码的键
     * @return array
     */
    function base64_decode_array(array $data, array $except = [])
    {
        foreach ($data as $key => $value) {
            if ($except[$key] ?? false) {
                continue;
            }

            if (is_array($value)) {
                $data[$key] = base64_decode_array($value);
            } elseif (is_string($value) && strlen($value)) {
                $data[$key] = base64_decode($value);
            }
        }

        return $data;
    }
}

if (! function_exists('lang')) {
    /**
     * 获取语言操作对象
     *
     * @param  string|null $alias
     * @return \App\Support\Lang
     */
    function lang(?string $alias = null)
    {
        return new Lang($alias);
    }
}

if (! function_exists('langcode')) {
    /**
     * 获取语言代码
     *
     * @param  string  $alias
     * @return string|null
     */
    function langcode(string $alias)
    {
        return lang($alias)->getLangcode();
    }
}

if (! function_exists('langname')) {
    /**
     * 获取语言名称
     *
     * @param  string  $alias
     * @param  string|null  $langcode 名称的语言版本
     * @return string|null
     */
    function langname(string $alias, ?string $langcode = null)
    {
        return lang($alias)->getLangname($langcode);
    }
}

if (! function_exists('langname_by_chinese')) {
    /**
     * 返回已添加语言的中文名称
     *
     * @param  string  $alias
     * @param  string|null  $langcode 语言代码
     * @return string|null
     */
    function langname_by_chinese(string $code)
    {
        return lang()->getLangNameByChinese($code);
    }
}

if (! function_exists('current_lang_code')) {
    /**
     * 获取当前语言代码
     *
     * @param  string $url url
     * @return string 代码
     */
    function current_lang_code(string $url)
    {
        if (!$url) {
            return config('lang.frontend');
        }
        $code = array_keys(config('lang.available'));
        $url = explode('/', trim($url));
        if (isset($url[1]) && in_array($url[1], $code)) return $url[1];

        return config('lang.frontend');
    }
}

if (! function_exists('cast')) {
    function cast($value, $caster, $force = true)
    {
        return Types::cast($value, $caster, $force);
    }
}

if (! function_exists('short_url')) {
    /**
     * 生成一个短 url （不带域名）
     *
     * @param  array|string  $name
     * @param  mixed  $parameters
     * @return string
     */
    function short_url($name, $parameters = [])
    {
        if (is_array($name)) {
            $parameters = $name[1] ?? [];
            $name = $name[0] ?? null;
        }
        return route($name, $parameters, false);
    }
}

if (! function_exists('under_route')) {
    function under_route($route, $path)
    {
        $url = short_url($route);
        // return $path == $url || strpos($path, $url.'/') === 0;
        return $path == $url;
    }
}

if (! function_exists('view_with_langcode')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string|null  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function view_with_langcode($view = null, $data = [], $mergeData = [])
    {
        $factory = app(ViewFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        $data = array_merge([
            'langcode' => langcode('content'),
        ], $data);

        return $factory->make($view, $data, $mergeData);
    }
}

if (! function_exists('is_json')) {
    function is_json($value)
    {
        if (! is_scalar($value) && ! method_exists($value, '__toString')) {
            return false;
        }

        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (! function_exists('last_modified')) {
    function last_modified($path)
    {
        if (is_file($path)) {
            return app('files')->lastModified($path);
        } elseif (is_dir($path)) {
            $fs = app('files');
            $lastModified = 0;
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $file) {
                $modified = $fs->lastModified($file->getRealPath());
                if ($modified > $lastModified) {
                    $lastModified = $modified;
                }
            }
            return $lastModified;
        }

        return null;
    }
}

if (! function_exists('str_diff')) {
    function str_diff($str1, $str2)
    {
        $diff = str_replace(str_split($str1), '', $str2);
        return strlen($diff);
    }
}

if (! function_exists('real_args')) {
    /**
     * 格式化传入参数
     *
     * @param array $args 文件名
     * @return array
     */
    function real_args(array $args)
    {
        // 如果只有一个参数，而且是一个数组，则假设该数组才是用户真正想要传入的参数
        if (count($args) === 1 && is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        return $args;
    }
}

if (! function_exists('short_md5')) {
    /**
     * @return string
     */
    function short_md5(string $input)
    {
        return substr(md5($input), 8, 16);
    }
}

if (! function_exists('safe_get_contents')) {
    /**
     * @param  string $file
     * @return string
     */
    function safe_get_contents(string $file)
    {
        return is_file($file) ? file_get_contents($file) : '';
    }
}

if (! function_exists('get_field_types')) {
    /**
     * 获取所有字段类型
     *
     * @param  string $scope
     * @return array
     */
    function get_field_types(string $scope = 'default')
    {
        return FieldTypeManager::details($scope);
    }
}

if (! function_exists('gather')) {
    /**
     * 在模型或模型集上执行 gather
     *
     * @param  mixed $items
     * @return \Illuminate\Support\Collection|array
     */
    function gather($items, array $keys = ['*'])
    {
        if ($items instanceof ModelBase) {
            return $items->gather($keys);
        }
        if ($items instanceof Collection) {
            return $items->map(function($item) use($keys) {
                return gather($item, $keys);
            });
        }
        return (array) $items;
    }
}

if (! function_exists('html_compress')) {
    /**
     * 压缩 html（简单的去除每行前导空白）
     *
     * @param  string $html
     * @return string
     */
    function html_compress($html)
    {
        return preg_replace('/>\n\s+/', ">\n", trim($html));
    }
}

if (! function_exists('format_value')) {
    /**
     * 格式化数据
     *
     * @param  string $value
     * @return data
     */
    function format_value($value)
    {
        try {
            return eval('return ' . $value . ';');
        } catch (\Throwable $e) {
            return $value;
        }
    }
}

if (! function_exists('specs_name')) {
    /**
     * 全部规格名称
     *
     * @return array
     */
    function specs_name()
    {
        return Spec::all()->map(function(Spec $spec) {
            return $spec->attributesToArray()['id'];
        })->all();
    }
}

if (!function_exists('array_unique_two')) {
    /**
     * 多维数组去重 原理 第二维数组转json去重再转回数组
     */
    function array_unique_two(array $data)
    {
        if (count($data) == count($data, 1)) {
            return array_unique($data);
        }

        foreach ($data as $key => $value) {
            ksort($value);
            $data[$key] = json_encode($value);
        }

        $data = array_unique($data);

        foreach ($data as $key => $value) {
            $data[$key] = json_decode($value, true);
        }

        return $data;
    }
}

if (!function_exists('custom_migration')) {
    /**
     * 创建表的迁移文件 安装项目后 所有后台创建的数据库的迁移文件
     * @param  string $table  表名称
     * @param  array  $data   字段信息
     * @param  array  $column 列的信息
     */
    function custom_migration(string $table, array $data, array $column)
    {
        // 文件存放路径
        $dir = base_path('database/custom');

        // 如果路径不存在 创建
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        // 生成表的迁移文件
        Artisan::call('make:migration ' . $table . ' --path=database/custom');

        // 获取文件名称
        $list = scandir($dir);
        foreach ($list as $key => $value) {
            if (strpos($value, $table . '.php') == 18) {
                $name = $value;
            }
        }

        // 定义文件路径
        $path = base_path('database/custom/' . $name);

        // 获取文件内容
        $file = file_get_contents($path);

        // 定义表信息
        $up = implode("\n", array_merge([
            'Schema::create(\'' . $table . '\', function (Blueprint $table) {',
            '            $column = json_decode(\'' . json_encode($column) . '\', true);'
        ], $data, ['        });']));

        $down = 'Schema::dropIfExists(\'' . $table . '\');';

        // 替换文件内容
        $file = str_replace(
            '    public function up()' . "\n" . '    {' . "\n" . '        //' . "\n" . '    }',
            '    public function up()' . "\n" . '    {' . "\n" . '        ' . $up . "\n" . '    }',
            $file
        );

        $file = str_replace(
            '    public function down()' . "\n" . '    {' . "\n" . '        //' . "\n" . '    }',
            '    public function down()' . "\n" . '    {' . "\n" . '        ' . $down . "\n" . '    }',
            $file
        );

        // 设置文件内容
        file_put_contents($path, $file);
        rename($path, $dir . '/' . str_replace('.php', '.txt', $name));
    }
}

if (!function_exists('url_get_contents')) {
    /**
     * 获取链接的页面内容
     * @param  string $url 链接
     * @return string
     */
    function url_get_contents(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSLVERSION,3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}