<?php

namespace Translate\Controllers;

use Translate\Translate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * 翻译功能
 * 
 * 一键翻译 翻译全部内容 先获取需要翻译的语言的代码 循环每个语言 获取全部页面 再获取每个页面包含的字段 再过滤不翻译的字段 再过滤有翻译后版本的字段 把字段的值拼接在一起 再把全部页面的值拼接在一起 同时创建每个语言的翻译任务 再循环获取结果 写入数据库 翻译完成
 * 页面翻译 前端获取页面的值请求接口 把值拼接后创建任务 循环获取结果 返回结果 翻译完成
 * 模板翻译 判断模板目录是否存在 复制需要的模板 获取模板内容 拼接 翻译 获取结果 写入文件 翻译完成
 */
class TranslateController extends Controller
{
    // 全部不翻译的字段 如果不区分语言 用一维数组 如果区分语言 用二维数组
    private $notFields = [
        'url',
        'meta_canonical',
        'image_src',
        'nav_icon',
        'applications',
        'projects',
        'installation',
        'data_pdf',
        'brochure_pdf',
        'nav_img',
        'index_pro',
        'pro',
        'list_icon',
        'muses',
        'athena',
        'apollo',
        'hephaistos',
        'triton',
        'astraios'
    ];

    // 全部不翻译的内容 如果不区分语言 用一维数组 如果区分语言 用二维数组
    private $notText = [
        'Argger',
        'ARGGER',
        'argger',
        'MUSES',
        'Muses',
        'muses',
        'ATHENA',
        'Athena',
        'athena',
        'APOLLO',
        'Apollo',
        'apollo',
        'HEPHAISTOS',
        'Hephaistos',
        'hephaistos',
        'TRITON',
        'Triton',
        'triton',
        'ASTRAIOS',
        'Astraios',
        'astraios'
    ];

    // 指定翻译结果 str_replace函数 可能会替换不需要替换的内容 如果不区分语言 用一维数组 如果区分语言 用二维数组
    private $replace = [
        'cn' => [
            'Argger' => '鉑格'
        ]
    ];

    // 前面加语言代码的url
    private $url = [
        '/index.html'
    ];

    // 执行时间
    private $time = [];

    // 不翻译的内容被替换的记录
    private $list = [];

    // 把所有内容拼接在一起时用的切割符号 第一个用于切割字段 第二个用于切割页面
    private $cutting = [
        '<div class="translate-field-cutting"></div>',
        '<div class="translate-page-cutting"></div>'
    ];

    // 项目里定义的转换后的语言代码
    private $code = '';

    // 错误信息
    private $error = [];

    /**
     * 翻译全部字段
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        if (!config('lang.multiple')) return response('没有开启多语言');

        $id = $request->input('id', '');
        $id = explode(',', $id);

        if (count($id) == 0) return response('没有要翻译的页面');

        $list   = [];
        $result = [];
        $front  = config('lang.frontend');

        foreach (config('lang.available') as $key => $value) {
            if ($value['translatable'] && $key != $front) $list[] = $key;
        }

        if (count($list) == 0) return response('没有要翻译的语言');

        // 过滤完开始翻译
        // 创建保存翻译id和状态的数组 状态默认false表示未翻译完成
        $taskid = [];
        $status = [];
        $error = [];

        // 同时处理数据创建任务并获取taskid
        foreach ($list as $key => $value) {
            // 创建任务
            $taskid[$value] = $this->allLangCreate($front, $value, $id);

            // 如果是字符串 字符串为报错信息
            if (is_string($taskid[$value])) {
                return response($taskid[$value]);
            }

            // 如果是null 表示没有要翻译的内容 不参与后续执行
            elseif (is_null($taskid[$value])) {
                unset($taskid[$value]);
            }

            // 创建成功 记录
            else {
                $this->time[$value] = [time()];
                $status[$value] = false;
            }
        }

        if (count($taskid) == 0) {
            return response([]);
        }

        // 创建死循环获取翻译结果
        while (true) {
            // 3秒后开始获取结果
            sleep(3);

            // 循环每个id获取结果
            foreach ($taskid as $key => $value) {
                // 如果这个语言编码的状态为真 表示已经获取到结果并修改了数据库 跳过这个语言
                if ($status[$key]) continue;

                // 用id获取结果 结果为数组表示获取成功 结果为null表示还在翻译 结果为false表示翻译失败
                $data = $this->get($value);

                // 如果结果是字符串 修改语言的状态和数据库
                if (is_string($data)) {
                    $this->time[$key][1] = time();
                    $status[$key] = true;
                    $result[$key] = $this->allLangSet($data, $front, $key, $id);
                } 

                // 如果是null 跳过本次循环 3秒后继续获取结果
                elseif (is_null($data)) {
                    continue;
                }

                // 如果是false 记录这个语言翻译失败
                elseif ($data === false) {
                    $error[$key] = false;
                }
            }

            // 如果全部语言的状态都为真 说明全部语言翻译完成 终止循环
            if (count($status) == count(array_filter($status))) {
                break;
            }

            // 如果翻译完成的结果数量加上翻译失败的结果数量等于全部语言的数量 表示全部语言都获取到了结果 终止循环
            elseif (count(array_filter($status)) + count($error) == count($status)) {
                break;
            }
        }

        return response(['time' => $this->time, 'update' => $result]);
    }

    /**
     * 翻译指定内容
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batch(Request $request)
    {
        // 获取数据
        $from   = config('lang.content');
        $to     = $request->input('to');
        $text   = json_decode($request->input('text'), true);

        if ($from == $to || count($text) == 0) {
            return $text;
        }

        // 过滤不翻译的内容
        foreach ($text as $key => $value) {
            if (in_array($key, $this->getNotFields())) {
                unset($text[$key]);
            }
        }

        // 获取翻译后内容
        $html = $this->html(implode($this->cutting[0], $text), $from, $to);

        // 如果是数组 则0下标是错误信息
        if (is_array($html)) return $html[0];

        // 切割结果
        $html = explode($this->cutting[0], $html);

        // 判断数量
        if (count($html) != count($text)) {
            return '翻译后内容数量不一致';
        }

        // 把结果按顺序替换翻以前的内容
        $i = 0;
        foreach ($text as $key => $value) {
            $text[$key] = $html[$i];
            $i++;
        }

        return $text;
    }

    /**
     * 复制模板并翻译
     * 
     * @param  string $code 翻译后的语言
     * @return \Illuminate\Http\Response
     */
    public function tpl(string $code)
    {
        // 记录时间
        $this->time[0] = [time()];

        // 模板路径
        $path = base_path('../themes/frontend/template/');

        // 如果目录已经存在 不执行任何操作
        if (is_dir($path . $code)) return response('目录已存在，请先删除目录');

        // 获取模板目录里的文件
        $list = scandir($path);

        // 过滤不复制的文件 复制
        foreach ($list as $key => $value) {
            if ($value == '.' || $value == '..') continue;
            if ($value == 'google-sitemap.twig') continue;

            if (is_dir($path . $value)) {
                if ($value != 'message' && $value != 'specs') continue;
            }

            if (is_file($path . $value)) {
                if (pathinfo($path . $value)['extension'] != 'twig') continue;
            }

            $this->tplCopy($path . $value, $path . $code . '/' . $value);
        }

        // 获取目录下需要翻译的文件路径
        $file = $this->tplFile($path . $code);

        $html = [];

        // 获取文件内容
        foreach ($file as $key => $value) {
            $html[] = file_get_contents($value);
        }

        // 翻译内容获取结果
        $html = $this->html(implode($this->cutting[1], $html), langcode('frontend'), $code);

        if (is_array($html)) return response($html[0]);

        // 切割结果
        $html = explode($this->cutting[1], $html);

        // 按顺序写入文件
        foreach ($file as $key => $value) {
            $handle = fopen($value, 'w');
            fwrite($handle, $html[$key]);
            fclose($handle);
        }

        // 记录时间
        $this->time[0][] = time();

        // 返回修改过的文件的路径
        return response(['time' => $this->time, 'file' => $file]);
    }

    /**
     * 复制模板文件
     * 
     * @param  string $old 被复制的文件路径
     * @param  string $new 复制后的文件路径
     */
    private function tplCopy(string $old, string $new)
    {
        // 复制的要求 old是一个文件 new不是文件也不是文件夹 防止覆盖
        if (is_file($old) && !is_file($new) && !is_dir($new)) {
            // new所在的目录不存在则创建
            if (!is_dir(dirname($new))) mkdir(dirname($new));
            copy($old, $new);
        }

        // 如果复制的是文件夹 则需要递归复制
        if (is_dir($old) && !is_file($new)) {
            if (!is_dir($new)) mkdir($new);
            $list = scandir($old);
            unset($list[0]);
            unset($list[1]);
            foreach ($list as $key => $value) {
                $this->tplCopy($old . '/' . $value, $new . '/' . $value);
            }
        }
    }

    /**
     * 获取模板里需要翻译的文件的路径
     * 
     * @param  string $path 模板路径
     * @return array  文件路径数组
     */
    private function tplFile(string $path)
    {
        $file = [];

        // 获取路径下的文件
        $list = scandir($path);
        foreach ($list as $key => $value) {
            if ($value == '.' || $value == '..') continue;

            if (is_dir($path . '/' . $value)) continue;
            $file[] = $path . '/' . $value;
        }

        // 获取路径下的表单文件
        $list2 = scandir($path . '/message/form');
        foreach ($list2 as $key => $value) {
            if ($value == '.' || $value == '..') continue;

            if (is_dir($path . '/message/form/' . $value)) continue;
            $file[] = $path . '/message/form/' . $value;
        }

        return $file;
    }

    /**
     * 翻译全部字段里创建其中一个语言的翻译任务
     * 
     * @param  string $from 源语言
     * @param  string $to   目标语言
     * @param  array  $id   被翻译的页面的id
     * @return array  翻译任务的taskid和缓存文件的路径
     */
    private function allLangCreate(string $from, string $to, array $id)
    {
        // 结果
        $list = [];

        // 循环获取每个页面的字段内容
        foreach ($id as $key => $value) {
            $data = $this->allLangPage($value, $from, $to);
            if (!$data) continue;
            $list[] = implode($this->cutting[0], $data);
        }

        if (count($list) == 0) return null;

        // 所有结果拼接到一起
        $html = implode($this->cutting[1], $list);

        // 创建任务 获取taskid
        $data = $this->create($html, $from, $to);

        return [
            $data['data']['body']['TaskId'],
            $data['file']
        ];
    }

    /**
     * 翻译全部字段里获取一个页面需要翻译的内容
     * 
     * @param  string $id   页面id
     * @param  string $from 源语言
     * @param  string $to   目标语言
     * @return array  需要翻译的内容
     */
    private function allLangPage(string $id, string $from, string $to)
    {
        // 结果
        $list = [];

        // 获取页面的全部字段
        $fields = Db::table('node_fields')->pluck('id')->toArray();

        // 过滤不翻译的字段
        $fields = array_diff($fields, $this->getNotFields());

        // 判断title是否存在翻译版本
        $check = Db::table('node_translations')->where('entity_id', $id)->where('langcode', $to)->exists();

        // 没有翻译版本 把内容放进结果里
        if (!$check) {
            $list['title'] = Db::table('nodes')->where('id', $id)->value('title');
        }

        // 循环每个字段
        foreach ($fields as $key => $value) {
            // 判断字段是否存在翻译版本
            $check = Db::table('node__' . $value)->where('entity_id', $id)->where('langcode', $to)->exists();

            // 没有翻译版本 把内容放进结果里
            if (!$check) {
                $list[$value] = Db::table('node__' . $value)->where('entity_id', $id)->where('langcode', $from)->value($value);
            }
        }

        // 过滤空字符串 返回结果
        return array_filter($list);
    }

    /**
     * 翻译全部字段里设置其中一个语言的翻译结果
     * 
     * @param  string $html 翻译后的html
     * @param  string $from 源语言
     * @param  string $to   目标语言
     * @param  array  $id   被翻译的页面的id
     * @return array  修改记录
     */
    private function allLangSet(string $html, string $from, string $to, array $id)
    {
        // 结果
        $list = [];

        // 切割翻译后的内容
        $html = explode($this->cutting[1], $html);
        foreach ($html as $key => $value) {
            $html[$key] = explode($this->cutting[0], $value);
        }

        // 循环获取每个页面的字段内容
        foreach ($id as $key => $value) {
            $data = $this->allLangPage($value, $from, $to);
            if (!$data) {
                unset($id[$key]);
                continue;
            }

            // 取出翻译后结果里的第一个
            $html2 = array_splice($html, 0, 1);

            // 修改数据库
            $id[$key] = [$value, $this->allLangUpdate($data, $html2[0], $value, $from, $to)];
        }

        // 返回修改记录
        return array_values($id);
    }

    /**
     * 翻译全部字段里修改一个页面的翻译结果
     * 
     * @param  array  $old  需要翻译的内容
     * @param  array  $new  翻译后的内容
     * @param  string $id   页面id
     * @param  string $from 源语言
     * @param  string $to   目标语言
     * @return array  修改的字段
     */
    private function allLangUpdate(array $old, array $new, string $id, string $from, string $to)
    {
        // 把翻译后的内容按顺序覆盖到翻以前的内容上
        $i = 0;
        foreach ($old as $key => $value) {
            $old[$key] = $new[$i];
            $i++;
        }

        // 修改数据库
        foreach ($old as $key => $value) {
            switch ($key) {
                case 'title':
                    $data = Db::table('nodes')->where('id', $id)->first();

                    Db::table('node_translations')->insert([
                        'entity_id'     => intval($id),
                        'mold_id'       => $data->mold_id,
                        'title'         => $value,
                        'view'          => $data->view,
                        'is_red'        => $data->is_red,
                        'is_green'      => $data->is_green,
                        'is_blue'       => $data->is_blue,
                        'langcode'      => $to,
                        'created_at'    => date('Y-m-d H:i:s')
                    ]);
                    break;

                default:
                    Db::table('node__' . $key)->insert([
                        'entity_id'     => $id,
                        $key            => $value,
                        'langcode'      => $to,
                        'created_at'    => date('Y-m-d H:i:s', time())
                    ]);
                    break;
            }
        }

        // 返回修改的字段
        return array_keys($old);
    }

    /**
     * 对一段html进行翻译并返回结果
     * 
     * @param  string $html 被翻译的html
     * @param  string $from 源语言
     * @param  string $to   目标语言
     * @return string|array 字符串表示翻译后的html 数组的0下标为错误信息
     */
    private function html(string $html, string $from, string $to)
    {
        // 创建任务
        $data = $this->create($html, $from, $to);

        $id = $data['data']['body']['TaskId'];

        $file = $data['file'];

        // 循环获取结果
        while (true) {
            sleep(3);

            $data = $this->get([$id, $file]);

            if (is_string($data)) {
                return $data;
            } elseif (is_null($data)) {
                continue;
            } elseif ($data === false) {
                return $this->error;
            }
        }

        return $data;
    }

    /**
     * 创建翻译任务
     * 
     * @param  string $html 被翻译的html
     * @param  string $from 源语言
     * @param  string $to   目标语言
     * @return array  翻译任务的taskid和缓存文件的路径
     */
    private function create(string $html, string $from, string $to)
    {
        // 记录语言代码
        $this->code = $to;

        // 获取内容里不翻译的内容
        $except = $this->except($html);

        // 把内容替换为随机数字 并记录
        $list = [];
        foreach ($except as $key => $value) {
            $number = $this->getNumber($html);
            $html = str_replace($value, $number, $html);
            $list[$number] = $value;
        }
        $this->list = $list;

        // 定义需要的数据
        $url    = env('APP_URL');
        // $html   = html_entity_decode($html);
        $to     = $to == 'zh-Hans' ? 'zh' : $to;
        $path   = str_replace('system', '', base_path());
        $file   = md5(strval(time()) . strval(mt_rand(10000, 99999))) . '.html';

        // 创建文档
        $result = touch($path . $file);
        if (!$result) return 'html缓存文件生成失败';

        // 写入文档
        $handle = fopen($path . $file, 'w');
        fwrite($handle, $html);
        fclose($handle);

        // 创建翻译任务
        $url .= '/' . $file;
        $data = Translate::create($url, $from, $to);
        return [
            'file'  => $path . $file,
            'data'  => $data
        ];
    }

    /**
     * 获取翻译结果
     * 
     * @param  array $data 翻译任务的taskid和缓存文件的路径
     * @return null|bool|string null表示翻译任务尚未完成 false表示翻译失败 string为翻译结果
     */
    private function get(array $data)
    {
        // 获取结果
        $file = $data[1];
        $data = Translate::get($data[0]);

        // 如果状态是准备或翻译中表示翻译任务尚未完成 返回null
        if ($data['body']['Status'] == 'ready' || $data['body']['Status'] == 'translating') {
            return null;
        }

        // 如果状态是错误 返回false
        elseif ($data['body']['Status'] == 'error') {
            $this->error[] = $data['body']['TranslateErrorMessage'];
            return false;
        }

        // 删除本地文档
        if (file_exists($file)) unlink($file);

        // 获取翻译后内容并转义
        $html = file_get_contents($data['body']['TranslateFileUrl']);
        // $html = html_entity_decode($html);

        // 获取不翻译的内容
        $except = $this->except($html);

        // 根据记录恢复
        foreach ($this->list as $key => $value) {
            $html = str_replace($key, $value, $html);
        }

        // 指定翻译结果
        foreach ($this->replace as $key => $value) {
            $html = str_replace($key, $value, $html);
        }

        // 页面里引入的路径前面添加语言代码 其他地方需要手动修改
        $html = str_replace('{% extends "_layout.twig" %}', '{% extends "' . $this->code . '/_layout.twig" %}', $html);
        $html = str_replace('{% use "_blocks.twig" %}', '{% use "' . $this->code . '/_blocks.twig" %}', $html);

        // 调用表单时传参加上语言代码
        $pattern = '/' . '{{ forms\(' . '([\w\W]*?)' . '\).render\(\)\|raw }}' . '/';
        $matches = [];
        preg_match_all($pattern, $html, $matches);
        foreach ($matches[0] as $key => $value) {
            $html = str_replace($value, str_replace('render()', 'render([], \'' . $this->code . '\')', $value), $html);
        }

        // 所有首页的链接加上语言代码
        foreach ($this->url as $key => $value) {
            $html = str_replace('href="' . $value . '"', 'href="/' . $this->code . $value . '"', $html);
        }

        // 搜索表单添加隐藏表单
        $pattern = '/' . '<form action="\/search" method="get"' . '([\w\W]*?)' . '<\/form>' . '/';
        $matches = [];
        preg_match_all($pattern, $html, $matches);
        foreach ($matches[0] as $key => $value) {
            $html = str_replace($value, str_replace('</form>', '<input type="hidden" name="lang" value="' . $this->code . '"></form>', $value), $html);
        }

        return $html;
    }

    /**
     * 获取不需要翻译的内容
     * 
     * @param  string $html 被翻译的html
     * @return array  html里不需要翻译的内容
     */
    private function except(string $html)
    {
        // 正则获取两个字符之间的字符
        $wrap = [
            ['{{ \'{{\' }}', '{{ \'}}\' }}'],
            ['{{', '}}'],
            ['{%', '%}'],
            ['{#', '#}'],
            ['data-dot="', '"']
        ];

        $except = [];

        foreach ($wrap as $key => $value) {
            $pattern = '/' . $value[0] . '([\w\W]*?)' . $value[1] . '/';
            $matches = [];
            preg_match_all($pattern, $html, $matches);
            $except = array_merge($except, $matches[0]);
        }

        // 返回内容里全部不翻译的内容
        return array_values(array_unique(array_merge($except, array_keys($this->getReplace()), $this->getNotText())));
    }

    /**
     * 获取一个一段html代码里不曾出现过的随机数
     * 
     * @param  string $html 被翻译的html
     * @return string 随机数
     */
    private function getNumber(string $html)
    {
        // 创建随机数
        $number = strval(mt_rand(100000000, 999999999));

        // 如果内容里已经存在该随机数 再次调用本身 否则返回随机数
        if (strstr($html, $number)) {
            return $this->getNumber($html);
        } else {
            return $number;
        }
    }

    /**
     * 获取不翻译的字段
     * 
     * @return array
     */
    private function getNotFields()
    {
        return count($this->notFields) == count($this->notFields, 1) ? $this->notFields : ($this->notFields[$this->code] ?? []);
    }

    /**
     * 获取不翻译的内容
     * 
     * @return array
     */
    private function getNotText()
    {
        return count($this->notText) == count($this->notText, 1) ? $this->notText : ($this->notText[$this->code] ?? []);
    }

    /**
     * 获取指定的翻译结果
     * 
     * @return array
     */
    private function getReplace()
    {
        return count($this->replace) == count($this->replace, 1) ? $this->replace : ($this->replace[$this->code] ?? []);
    }
}
