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
    ];

    // 全部不翻译的内容 如果不区分语言 用一维数组 如果区分语言 用二维数组
    private $notText = [
        'Hebei Yingbo Safe Boxes Co., Ltd.',
        'Hebei UTOP Technologies Co.,Ltd.',
        'YINGBO safe',
        'Yingbo safe',
        'yingbo safe',
        'YINGBO',
        'Yingbo',
        'yingbo',
        'ANNA',
        'Anna',
        'anna'
    ];

    // 指定翻译结果 str_replace函数 可能会替换不需要替换的内容 如果不区分语言 用一维数组 如果区分语言 用二维数组
    private $replace = [
        'cn' => [
            'Argger' => '鉑格'
        ]
    ];

    // 前面加语言代码的url
    private $url = [
        '/index.html',
    ];

    // 行元素
    private $lineElement = ['a', 'b', 'em', 'font', 'i', 'span', 'strong'];

    // 执行时间
    private $time = [];

    // 不翻译的内容被替换的记录
    private $list = [];

    // 标识 第一个用于切割字段 第二个用于切割页面 第三个用于替换空格
    private $cutting = [
        '<div class="translate-field-cutting"></div>',
        '<div class="translate-page-cutting"></div>',
        '<div class="translate-space"></div>'
    ];

    // 项目里定义的转换后的语言代码
    private $code = '';

    // 错误信息
    private $error = [];

    // 缓存文件路径
    private $path;

    // 缓存文件网址
    private $website;

    function __construct()
    {
        $this->path     = base_path('../translate/');
        $this->website  = env('APP_URL') . '/translate';
        if (!is_dir($this->path)) mkdir($this->path);

        // 如果证书不能用用这个 前提在同一个空间下
        // $this->path     = '/home2/wiremesh/hecland.com/';
        // $this->website  = 'http://www.hecland.com';
    }

    /**
     * 翻译全部字段
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        if (!config('lang.multiple')) return response('没有开启多语言');

        $id = DB::table('nodes')->pluck('id')->toArray();

        if (count($id) == 0) return response('没有要翻译的页面');

        $list   = [];
        $result = [];
        $front  = config('lang.frontend');

        foreach (config('lang.available') as $key => $value) {
            if ($value['translatable'] && $key != $front) $list[] = $key;
        }

        if (count($list) == 0) return response('没有要翻译的语言');

        // 记录翻译语言
        $this->code = $list;

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
            return response('没有要翻译的内容');
        }

        $taskid['type'] = 'all';
        return response($taskid);
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
            return '不需要翻译';
        }

        // 记录翻译语言
        $this->code = $to;

        // 过滤不翻译的内容
        foreach ($text as $key => $value) {
            if (in_array($key, $this->getNotFields($to))) {
                unset($text[$key]);
            }
        }

        // 创建任务
        $result = $this->create(implode($this->cutting[0], $text), $from, $to);
        $result['id'] = $result['data']['body']['TaskId'];
        $result['code'] = $to;
        $result['type'] = 'batch';
        $result['field'] = array_keys($text);
        unset($result['data']);

        // 返回任务信息
        return response($result);
    }

    /**
     * 复制模板并翻译
     * 
     * @param  string $code 翻译后的语言
     * @return \Illuminate\Http\Response
     */
    public function tpl(string $code)
    {
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

        // 创建任务
        $result = $this->create(implode($this->cutting[1], $html), langcode('frontend'), $code);
        $result['id'] = $result['data']['body']['TaskId'];
        $result['code'] = $code;
        $result['type'] = 'tpl';
        unset($result['data']);

        // 返回任务信息
        return response($result);
    }

    /**
     * 根据id获取结果 如果翻译完成 处理数据
     * 
     * @param  \Illuminate\Http\Request  $request
     */
    public function result(Request $request)
    {
        $data = $request->all();

        switch ($data['type']) {
            case 'all':
                return $this->resultAll($data);
                break;

            case 'batch':
                $result = $this->get([$data['id'], $data['file']], $data['code'], $data['log']);
                if (is_array($result) && count($result) == 1) return $result[0];
                return $this->resultBatch($data, $result);
                break;

            case 'tpl':
                $result = $this->get([$data['id'], $data['file']], $data['code'], $data['log']);
                if (is_array($result) && count($result) == 1) return $result[0];
                return $this->resultTpl($data, $result);
                break;

            default:
                return response('非法操作');
                break;
        }
    }

    /**
     * 根据结果处理模板
     * 
     * @param  array  $data 翻译接口返回的数据
     * @param  string $html 翻译的结果
     * @return \Illuminate\Http\Response
     */
    private function resultAll(array $data)
    {
        unset($data['type']);
        $front  = config('lang.frontend');
        $code = array_keys(config('lang.available'));

        foreach ($data as $key => $value) {
            if (!in_array($key, $code)) {
                unset($data[$key]);
            }
        }

        foreach ($data as $key => $value) {
            if (isset($value['result'])) {
                $data[$key] = $value['result'];
                continue;
            }

            $result = $this->get([$value['id'], $value['file']], $value['code'], $value['log']);
            if (is_array($result) && count($result) == 1) {
                $data[$key] = $result[0];
            } else {
                $id = DB::table('nodes')->pluck('id')->toArray();
                $this->allLangSet($result, $front, $key, $id);
                $data[$key] = 'translated';
            }
        }
        return response($data);
    }

    /**
     * 根据结果处理模板
     * 
     * @param  array  $data 翻译接口返回的数据
     * @param  string $html 翻译的结果
     * @return \Illuminate\Http\Response
     */
    private function resultBatch(array $data, string $html)
    {
        // 切割结果
        $html = explode($this->cutting[0], $html);

        // 判断数量
        if (count($html) != count($data['field'])) {
            return '翻译后内容数量不一致';
        }

        // 把结果按顺序替换翻以前的内容
        $i = 0;
        $text = [];
        foreach ($data['field'] as $key => $value) {
            $text[$value] = $html[$i];
            $i++;
        }

        return response($text);
    }

    /**
     * 根据结果处理模板
     * 
     * @param  array  $data 翻译接口返回的数据
     * @param  string $html 翻译的结果
     * @return \Illuminate\Http\Response
     */
    private function resultTpl(array $data, string $html)
    {
        // 模板路径
        $path = base_path('../themes/frontend/template/');

        // 切割结果
        $html = explode($this->cutting[1], $html);

        // 获取目录下需要翻译的文件路径
        $file = $this->tplFile($path . $data['code']);

        // 按顺序写入文件
        foreach ($file as $key => $value) {
            $handle = fopen($value, 'w');
            fwrite($handle, $html[$key]);
            fclose($handle);
        }

        // 返回修改过的文件的路径
        return response(['file' => $file]);
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
        $data['id'] = $data['data']['body']['TaskId'];
        $data['code'] = $to;
        unset($data['data']);

        return $data;
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
        $fields = array_diff($fields, $this->getNotFields($to));

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
     * 创建翻译任务
     * 
     * @param  string $html 被翻译的html
     * @param  string $from 源语言
     * @param  string $to   目标语言
     * @return array  翻译任务的taskid和缓存文件的路径
     */
    private function create(string $html, string $from, string $to)
    {
        // 替换特殊空格
        $html = str_replace(chr(0xC2).chr(0xA0), ' ', $html);
        $html = str_replace('&nbsp;', ' ', $html);

        // 获取内容里不翻译的内容
        $except = $this->except($html, $to);

        // 把内容替换为随机数字 并记录
        $list = [];
        foreach ($except as $key => $value) {
            $number = $this->getNumber($html);
            $html = str_replace($value, $number, $html);
            $list[$number] = $value;
        }
        $this->list[$to] = $list;

        // 定义需要的数据
        // $html   = html_entity_decode($html);
        $to     = $this->codeChange($to);
        $file   = md5(strval(time()) . strval(mt_rand(10000, 99999))) . '.html';

        if ($to != 'zh' && $to != 'zh-tw') {
            // 把行元素前后的空格用指定内容替换
            foreach ($this->lineElement as $key => $value) {
                $html = str_replace(' <' . $value . ' ', $this->cutting[2] . '<' . $value . ' ', $html);
                $html = str_replace(' <' . $value . '>', $this->cutting[2] . '<' . $value . '>', $html);
                $html = str_replace('</' . $value . '> ', '</' . $value . '>' . $this->cutting[2], $html);
            }
        }

        // 创建文档
        $result = touch($this->path . $file);
        if (!$result) return 'html缓存文件生成失败';

        // 写入文档
        $handle = fopen($this->path . $file, 'w');
        fwrite($handle, $html);
        fclose($handle);

        // 创建翻译任务
        $data = Translate::create($this->website . '/' . $file, $from, $to);

        // 写入日志
        $log = $this->log($this->path . $file, $data, 1);

        return [
            'file'  => $this->path . $file,
            'data'  => $data,
            'log'   => $log
        ];
    }

    /**
     * 获取翻译结果
     * 
     * @param  array  $data 翻译任务的taskid和缓存文件的路径
     * @param  string $code 目标语言
     * @param  string $log  日志名称
     * @return null|bool|string null表示翻译任务尚未完成 false表示翻译失败 string为翻译结果
     */
    private function get(array $data, string $code, string $log)
    {
        // 获取结果
        $file = $data[1];
        $data = Translate::get($data[0]);

        // 如果状态是准备或翻译中表示翻译任务尚未完成 返回结果
        if ($data['body']['Status'] == 'ready' || $data['body']['Status'] == 'translating') {
            return [$data['body']['Status']];
        }

        // 如果状态是错误 返回结果
        elseif ($data['body']['Status'] == 'error') {
            // 写入日志
            $this->log($file, $data, 2);

            $this->error[] = $data['body']['TranslateErrorMessage'];
            return [$data['body']['Status']];
        }

        // 写入日志
        $this->log($file, $data, 2);

        $log = json_decode(file_get_contents($this->path . 'translate_log/' . $log . '/result1.json'), true);

        // $this->notFields    = $log['notFields'];
        // $this->notText      = $log['notText'];
        // $this->replace      = $log['replace'];
        // $this->url          = $log['url'];
        // $this->lineElement  = $log['lineElement'];
        $this->list         = $log['list'];
        // $this->cutting      = $log['cutting'];
        $this->code         = $log['code'];

        // 获取翻译后内容并转义
        $html = url_get_contents($data['body']['TranslateFileUrl']);
        $html = html_entity_decode($html);

        // 根据记录恢复
        foreach ($this->list[$code] as $key => $value) {
            $html = str_replace($key, $value, $html);
        }

        // 指定翻译结果
        foreach ($this->getReplace($code) as $key => $value) {
            $html = str_replace($key, $value, $html);
        }

        // 页面里引入的路径前面添加语言代码 其他地方需要手动修改
        $html = str_replace('{% extends "_layout.twig" %}', '{% extends "' . $code . '/_layout.twig" %}', $html);
        $html = str_replace('{% use "_blocks.twig" %}', '{% use "' . $code . '/_blocks.twig" %}', $html);

        // 调用表单时传参加上语言代码
        $pattern = '/' . '{{ forms\(' . '([\w\W]*?)' . '\).render\(\)\|raw }}' . '/';
        $matches = [];
        preg_match_all($pattern, $html, $matches);
        foreach ($matches[0] as $key => $value) {
            $html = str_replace($value, str_replace('render()', 'render([], \'' . $code . '\')', $value), $html);
        }

        // 所有链接加上语言代码
        foreach ($this->getUrl($html) as $key => $value) {
            $html = str_replace('href="' . $value . '"', 'href="/' . $code . $value . '"', $html);
        }

        // 搜索表单添加隐藏表单
        $pattern = '/' . '<form action="\/search" method="get"' . '([\w\W]*?)' . '<\/form>' . '/';
        $matches = [];
        preg_match_all($pattern, $html, $matches);
        foreach ($matches[0] as $key => $value) {
            $html = str_replace($value, str_replace('</form>', '<input type="hidden" name="lang" value="' . $code . '"></form>', $value), $html);
        }

        // 恢复行元素前后的空格
        $html = str_replace($this->cutting[2], ' ', $html);

        $html = str_replace('&#39;', '\'', $html);

        return $html;
    }

    /**
     * 获取不需要翻译的内容
     * 
     * @param  string $html 被翻译的html
     * @param  string $code 目标语言
     * @return array  html里不需要翻译的内容
     */
    private function except(string $html, string $code)
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
        return array_values(array_unique(array_merge($except, array_keys($this->getReplace($code)), $this->getNotText($code))));
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
        $number = strval(mt_rand(1000000, 9999999));

        // 如果内容里已经存在该随机数或随机数中存在1 再次调用本身 否则返回由1开始和结束的随机数 这样每个随机数都是1xxxxxxx1
        if (strstr($html, $number) || strstr($number, '1') || in_array($number, array_keys($this->list)) !== false) {
            return $this->getNumber($html);
        } else {
            return '1' . $number . '1';
        }
    }

    /**
     * 获取不翻译的字段
     * @param string $code 目标语言
     * @return array
     */
    private function getNotFields(string $code)
    {
        return count($this->notFields) == count($this->notFields, 1) ? $this->notFields : ($this->notFields[$code] ?? []);
    }

    /**
     * 获取不翻译的内容
     * @param string $code 目标语言
     * @return array
     */
    private function getNotText(string $code)
    {
        return count($this->notText) == count($this->notText, 1) ? $this->notText : ($this->notText[$code] ?? []);
    }

    /**
     * 获取指定的翻译结果
     * @param string $code 目标语言
     * @return array
     */
    private function getReplace(string $code)
    {
        return count($this->replace) == count($this->replace, 1) ? $this->replace : ($this->replace[$code] ?? []);
    }

    /**
     * 获取所有本地链接
     * @param  string $html 翻译内容
     * @return array
     */
    private function getUrl(string $html)
    {
        // 正则获取所有href属性
        $pattern = '/' . 'href="' . '([\w\W]*?)' . '"' . '/';
        $matches = [];
        preg_match_all($pattern, $html, $matches);
        $list = $matches[1];

        foreach ($list as $key => $value) {
            if (substr($value, 0, 1) != '/') {
                unset($list[$key]);
                continue;
            }

            if (substr($value, -5) != '.html' && strstr($value, '.html#') === false) {
                unset($list[$key]);
                continue;
            }
        }

        return array_unique(array_merge($this->url, $list));
    }

    /**
     * 根据语言代码获取阿里云的语言代码
     * @param  string $code 语言代码
     * @return string
     */
    private function codeChange(string $code)
    {
        return config('lang.available')[$code]['code'];
    }

    /**
     * 翻译记录保存
     * @param  string $file   被翻译的文件路径
     * @param  array  $result 阿里云返回的结果
     */
    private function log(string $file, array $result, int $status)
    {
        // 定义日志目录 根目录下的translate_log文件夹
        $path = $this->path . 'translate_log';

        // 如果没有文件夹 创建
        if (!is_dir($path)) mkdir($path);

        // 定义本次日志保存的文件夹名称并创建 当前日期
        $name = date('YmdHis') . strval(mt_rand(10000, 99999));
        $path .= '/' . $name;
        mkdir($path);

        // 复制翻译前的html
        copy($file, $path . '/from.html');

        // 如果翻译成功 保存翻译后的内容
        if ($result['body']['Status'] == 'translated') {
            touch($path . '/to.html');
            file_put_contents($path . '/to.html', url_get_contents($result['body']['TranslateFileUrl']));
        }

        // 本次翻译的所有信息保存为json
        $data = [
            'notFields'     => $this->notFields,
            'notText'       => $this->notText,
            'replace'       => $this->replace,
            'url'           => $this->url,
            'lineElement'   => $this->lineElement,
            'list'          => $this->list,
            'cutting'       => $this->cutting,
            'code'          => $this->code,
            'form'          => $this->website . '/' . basename($file),
            'result'        => $result
        ];

        file_put_contents($path . '/result' . $status . '.json', json_encode($data, JSON_PRETTY_PRINT));

        return $name;
    }
}
