<?php
namespace Translate;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * 翻译
 */
class Translate
{
    // 域名
    private $domain;

    // api
    // private $api = [
    //     'https://api.vip/api/translate/translate',
    //     'https://api.vip/api/translate/create',
    //     'https://api.vip/api/translate/get'
    // ];
    private $api = [
        'https://www.shouqibucuo.com/api/translate/translate',
        'https://www.shouqibucuo.com/api/translate/create',
        'https://www.shouqibucuo.com/api/translate/get'
    ];

    // 源语言
    private $source = 'en';

    // 翻译语言
    private $target;

    // 一键翻译翻译的页面id
    private $nodes;

    // 不翻译的字段
    private $notFields;

    // 不翻译的内容
    private $notText;

    // 指定翻译的内容
    private $appoint;

    // 模板路径
    private $tplPath;

    // 翻译结果
    private $result;

    // 当前模式是不是直接翻译
    private $mode;

    // 接口错误或任务错误
    // $result = 'error';

    // 翻译成功 可能部分语言错误
    // $result = [
    //     'de'    => '<p></p>',   // 成功
    //     'es'    => false        // 失败
    // ];

    // 标识 第一个用于切割字段 第二个用于切割页面 第三个用于替换空格 第四个用于代替空页面的数据
    private $replace = [
        '<div class="translate-field-cutting"></div>',
        '<div class="translate-page-cutting"></div>',
        '<div class="translate-space"></div>',
        '<div class="translate-page-empty"></div>'
    ];

    // 全局缓存
    private $cache = [];

    // 代码转换
    private $code;

    // 工具
    private $tool;

    /**
     * 初始化一批成员属性
     */
    function __construct($result = true)
    {
        $this->domain       = request()->host();

        $this->source       = config('lang.translate');

        $this->notFields    = json_decode(config('translate.fields'), true);
        $this->notText      = json_decode(config('translate.text'), true);
        $this->appoint      = json_decode(config('translate.replace'), true);

        $this->notFields    = is_array($this->notFields) ? $this->notFields : [];
        $this->notText      = is_array($this->notText) ? $this->notText : [];
        $this->appoint      = is_array($this->appoint) ? $this->appoint : [];

        $this->tplPath      = base_path('../themes/frontend/template/');
        $this->mode         = $result;

        $this->tool         = config('translate.tool');

        $this->code         = json_decode(config('translate.code'), true);
        $this->code         = is_array($this->code) ? $this->code : [];
        $this->code         = $this->code[$this->tool] ?? [];
    }

    /**
     * 设置翻译语言
     * 
     * @param  string $code 翻译语言
     * @return $this
     */
    public function setTo(string|array $code)
    {
        if (is_string($code)) {
            // 源语言不能和翻译语言一致
            if ($code == $this->source) return $this;

            // 翻译语言必须在后台配置才能翻译
            if (!in_array($code, array_keys(config('lang.available')))) return $this;

            $this->target = [$code];
        }

        if (is_array($code)) {
            $code = array_values(array_intersect(array_keys(config('lang.available')), array_diff($code, [$this->source])));

            if (count($code) == 0) return $this;

            $this->target = $code;
        }

        return $this;
    }

    /**
     * 设置一键翻译翻译的页面id
     * 
     * @param  array $nodes 页面id
     * @return $this
     */
    public function setNodes(array $nodes)
    {
        // 只能设置数据库里存在的页面
        $nodes = Db::table('nodes')->whereIn('id', $nodes)->pluck('id')->toArray();

        $this->nodes = $nodes;
        return $this;
    }

    /**
     * 批量翻译
     * 
     * @return Response
     */
    public function batch(): JsonResponse
    {
        // 翻译
        $result = $this->start($this->batchBefore());

        if ($result !== true) return $result;

        return $this->end('batch');
    }

    /**
     * 翻译页面
     * 
     * @param  array $content 被翻译的内容
     * @return Response
     */
    public function page(array $content): JsonResponse
    {
        $this->cache['pageContent'] = $content;

        // 翻译
        $result = $this->start($this->pageBefore($content));

        if ($result !== true) return $result;

        return $this->end('page');
    }

    /**
     * 翻译模板
     * 
     * @return Response
     */
    public function tpl(): JsonResponse
    {
        // 翻译
        $result = $this->start($this->tplBefore(), function () {
            if (is_dir($this->tplPath . $this->target[0])) return $this->error('目录已存在，请先删除目录');
        });

        if ($result !== true) return $result;

        return $this->end('tpl');
    }

    /**
     * 根据数据获取翻译结果
     * 
     * @param  string $type    翻译的类型
     * @param  string $data    翻译的数据
     * @param  array  $content 翻译页面被翻译的内容
     * @return JsonResponse
     */
    public function result(string $type, string $data, array $pageContent = []): JsonResponse
    {
        if ($type == 'page') $this->cache['pageContent'] = $pageContent;

        $this->get($data);

        if ($this->result['status'] === true) {
            $this->result = $this->result['data'];
            $result = [];
            foreach ($this->result as $code => $data) {
                $code = $this->code($code, false);
                $result[$code] = $data;
            }
            $this->result = $result;
            return $this->end($type, true);
        }

        if ($this->result['status'] === false) {
            return $this->error($this->result['message']);
        }

        if ($this->result['status'] === null) {
            return $this->running($this->result['message'], $this->result['data']);
        }
    }

    public function batchSuccess(array $list)
    {
        foreach ($list as $key => $value) {
            $list[$key] = $value === true
            ? ['status' => true, 'message' => '翻译成功']
            : ['status' => false, 'message' => '翻译失败'];
        }
        return response()->json(['status' => true, 'data' => $list])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function pageSuccess(array $data)
    {
        return response()->json(['status' => true, 'data' => $data])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function tplSuccess()
    {
        return response()->json(['status' => true])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function success($data)
    {
        return response()->json(['status' => true, 'data' => $data])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    public function error(string $message)
    {
        return response()->json(['status' => false, 'message' => $message])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     * 任务正在运行的返回值
     * 
     * @param  string $status 状态信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function running(string $message, string $data): JsonResponse
    {
        return response()->json([
            'status'    => null,
            'message'   => $message,
            'data'      => $data
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     * 创建翻译任务并获取翻译结果
     * 
     * @param  string $html 被翻译的html
     */
    private function translate(string $html): void
    {
        $target = $this->target;
        foreach ($target as $key => $value) {
            $target[$key] = $this->code($value);
        }

        $result = post($this->api[0], [
            'html'          => $html,
            'source'        => $this->source,
            'target'        => $target,
            'not'           => $this->getNotText(),
            'appoint'       => $this->getAppoint(),
            'domain'        => $this->domain,
            'tool'          => $this->tool,
            'replace'       => $this->replace
        ], ['user-host: ' . $this->domain]);

        $result === false && $this->result = '翻译接口调用失败';

        $result !== false && is_null(json_decode($result, true)) && exit($result);

        $result = json_decode($result, true);

        $result !== null && $this->result = $result['status'] ? $result['data'] : $result['message'];
    }

    /**
     * 创建翻译任务并获取任务结果
     * 
     * @param  string $html 被翻译的html
     */
    private function create(string $html): void
    {
        $target = $this->target;
        foreach ($target as $key => $value) {
            $target[$key] = $this->code($value);
        }

        $result = post($this->api[1], [
            'html'          => $html,
            'source'        => $this->source,
            'target'        => $target,
            'not'           => $this->getNotText(),
            'appoint'       => $this->getAppoint(),
            'domain'        => $this->domain,
            'tool'          => $this->tool,
            'replace'       => $this->replace
        ], ['user-host: ' . $this->domain]);

        $result === false && $this->result = '翻译接口调用失败';

        $result !== false && is_null(json_decode($result, true)) && exit($result);

        $result = json_decode($result, true);

        $result !== null && $this->result = $result['status'] ? $result['data'] : $result['message'];
    }

    /**
     * 获取翻译结果
     * 
     * @param  string $data 翻译任务的数据
     */
    private function get(string $data): void
    {
        $result = post($this->api[2], ['data' => $data, 'tool' => $this->tool], ['user-host: ' . $this->domain]);

        $result === false && $this->result = '翻译接口调用失败';

        $result !== false && is_null(json_decode($result, true)) && exit($result);

        $this->result = json_decode($result, true);
    }

    /**
     * 批量翻译前处理页面数据返回需要翻译的内容
     * 
     * @return string
     */
    private function batchBefore(): string
    {
        $html = [];

        // 循环每个页面 获取每个页面需要翻译的内容
        foreach ($this->nodes as $id) {
            $data = $this->getPageContent($id);
            if (!$data) continue;
            $html[] = implode($this->replace[0], $data);
        }

        return count($html) == 0 ? '' : implode($this->replace[1], $html);
    }

    /**
     * 批量翻译完成后把结果写入对应的语言的数据库中
     * 
     * @param  array $data
     * @return array
     */
    private function batchAfter(array $data): array
    {
        foreach ($data as $code => $html) {
            $local = $this->code($code, false);

            str_replace($code, $local, $html);

            if ($html === false) continue;

            // 切割成每个页面的翻译结果
            $pages = explode($this->replace[1], $html);

            // 如果翻译后的页面数量和被翻译的页面数量不一致
            if (count($pages) != count($this->nodes)) {
                $data[$local] = false;
                continue;
            }

            foreach ($this->nodes as $key => $id) {
                $this->setPageContent($id, explode($this->replace[0], $pages[$key]), $local);
            }

            $data[$local] = true;
        }

        return $data;
    }

    /**
     * 翻译页面前处理页面数据返回需要翻译的内容
     * 
     * @param  array $html
     * @return ?string
     */
    private function pageBefore(array $html): ?string
    {
        // 去掉不需要翻译的字段
        foreach ($html as $key => $value) {
            if (in_array($key, $this->getNotFields())) {
                unset($html[$key]);
            }
        }

        return count($html) == 0 ? null : implode($this->replace[0], $html);
    }

    /**
     * 翻译页面完成后处理数据
     * 
     * @param  array  $old
     * @param  string $new
     * @return ?array
     */
    private function pageAfter(array $old, string $new): ?array
    {
        // 去掉不需要翻译的字段
        foreach ($old as $key => $value) {
            if (in_array($key, $this->getNotFields())) {
                unset($old[$key]);
            }
        }

        // 切割成每个字段的翻译结果
        $new = explode($this->replace[0], $new);

        // 如果翻译后的页面数量和被翻译的页面数量不一致
        if (count($new) != count($old)) return null;

        return array_combine(array_keys($old), $new);
    }

    /**
     * 翻译模板前处理模板数据返回需要翻译的内容
     * 
     * @return ?string
     */
    private function tplBefore(): ?string
    {
        // 所有需要翻译的文件的绝对路径
        $files = $this->getTplFilePath();

        $html = [];

        // 所有需要翻译的文件的内容
        foreach ($files as $file) $html[] = file_get_contents($file);

        return count($html) == 0 ? null : implode($this->replace[0], $html);
    }

    /**
     * 翻译模板完成后处理数据
     * 
     * @param  string $html
     * @return bool
     */
    private function tplAfter(string $html): bool
    {
        // 切割成每个文件的翻译结果
        $html = explode($this->replace[0], $html);

        // 所有需要翻译的文件的绝对路径
        $files = $this->getTplFilePath();

        // 如果翻译后的页面数量和被翻译的页面数量不一致
        if (count($html) != count($files)) return false;

        // 翻译后的模板文件写入内容
        foreach ($files as $key => $file) {
            $file = str_replace($this->tplPath . ($this->source == 'en' ? '' : $this->source . '/'), $this->tplPath . $this->target[0] . '/', $file);
            $this->setTplFileContent($file, $html[$key]);
        }

        $dir = $this->tplPath . ($this->source == 'en' ? '' : $this->source . '/') . 'message/content/';
        $list = array_slice(scandir($dir), 2);
        foreach ($list as $key => $file) {
            $source = $dir . $file;
            $target = str_replace($this->tplPath . ($this->source == 'en' ? '' : $this->source . '/'), $this->tplPath . $this->target[0] . '/', $source);
            $this->setTplFileContent($target, file_get_contents($source));
        }

        return true;
    }

    /**
     * 处理后开始翻译
     * 
     * @param  string $html   翻译的内容
     * @param  object $bofore 处理的函数
     * @return JsonResponse|bool
     */
    private function start(string $html, object $bofore = null): JsonResponse|bool
    {
        if ($bofore) {
            $bofore = $bofore();
            if ($bofore) return $bofore;
        }

        // 没有要翻译的内容
        if (!$html) return $this->error('没有要翻译的内容');

        // 创建任务并获取翻译的结果
        $this->mode ? $this->translate($html) : $this->create($html);

        return true;
    }

    /**
     * 翻译结束返回结果
     * 
     * @param  string $type 翻译的三种类型
     * @return JsonResponse
     */
    private function end(string $type, bool $result = false): JsonResponse
    {
        // 直接返回结果
        if ($this->mode || $result) {
            // 返回错误信息
            if (is_string($this->result)) return $this->error($this->result);

            switch ($type) {
                case 'batch':
                    // 翻译完成 把每个语言写入对应数据库表 获取写入结果
                    $result = $this->batchAfter($this->result);

                    // 所有页面都不需要翻译
                    if (!$result) return $this->error('没有要翻译的内容');

                    return $this->batchSuccess($result);
                    break;

                case 'page':
                case 'tpl':
                    // 翻译结果
                    if (isset($this->result[$this->code($this->target[0], true)])) {
                        $html = $this->result[$this->code($this->target[0], true)];
                    } else {
                        $html = $this->result[$this->target[0]];
                    }

                    $local = $this->target[0];
                    $tool = $this->code($local, true);
                    str_replace($tool, $local, $html);

                    // 翻译失败的处理
                    if (!$html) return $this->error('翻译失败');

                    // 翻译完成后处理数据
                    if ($type == 'page') $result = $this->pageAfter($this->cache['pageContent'], $html);
                    if ($type == 'tpl') $result = $this->tplAfter($html);

                    // 处理失败
                    if (!$result) return $this->error('翻译成功但处理失败');

                    // 翻译完成后处理数据
                    if ($type == 'page') return $this->pageSuccess($result);
                    if ($type == 'tpl') return $this->tplSuccess();
                    break;
            }
        }

        // 创建任务
        else {
            return json_decode($this->result, true) ? $this->success($this->result) : $this->error($this->result);
        }
    }

    /**
     * 获取不翻译的字段
     * 
     * @return array
     */
    private function getNotFields(): array
    {
        if (count($this->notFields) == count($this->notFields, 1)) {
            return $this->notFields;
        } else {
            if (is_string($this->target)) return $this->notFields[$this->target] ?? [];

            $list = [];
            foreach ($this->target as $key => $value) {
                if (isset($this->notFields[$value])) {
                    $list[$value] = $this->notFields[$value];
                }
            }
            return $list;
        }
    }

    /**
     * 获取不翻译的内容
     * 
     * @return array
     */
    private function getNotText(): array
    {
        if (count($this->notText) == count($this->notText, 1)) {
            return $this->notText;
        } else {
            if (is_string($this->target)) return $this->notText[$this->target] ?? [];

            $list = [];
            foreach ($this->target as $key => $value) {
                if (isset($this->notText[$value])) {
                    $list[$value] = $this->notText[$value];
                }
            }
            return $list;
        }
    }

    /**
     * 获取指定的翻译结果
     * 
     * @return array
     */
    private function getAppoint(): array
    {
        if (count($this->appoint) == count($this->appoint, 1)) {
            return $this->appoint;
        } else {
            if (is_string($this->target)) return $this->appoint[$this->target] ?? [];

            $list = [];
            foreach ($this->target as $key => $value) {
                if (isset($this->appoint[$value])) {
                    $list[$value] = $this->appoint[$value];
                }
            }
            return $list;
        }
    }

    /**
     * 获取一个页面需要翻译的内容
     * 
     * @param  int    $id   页面id
     * @param  string $code 语言代码
     * @return array
     */
    private function getPageContent(int $id, ?string $code = null): array
    {
        // 结果
        $list = [];

        // 获取页面的全部字段
        $fields = Db::table('node_fields')->pluck('id')->toArray();

        // 过滤不翻译的字段
        $fields = array_diff($fields, $this->getNotFields());

        // 判断title是否存在翻译版本
        $check = Db::table('node_translations')->where('entity_id', $id)->where('langcode', $code ?? $this->target[0])->exists();

        // 没有翻译版本 把内容放进结果里
        if (!$check) {
            if ($this->source == 'en') {
                $list['title'] = Db::table('nodes')->where('id', $id)->value('title');
            } else {
                $list['title'] = Db::table('node_translations')->where('entity_id', $id)->where('langcode', $this->source)->value('title');
            }
        }

        // 循环每个字段
        foreach ($fields as $key => $value) {
            // 判断字段是否存在翻译版本
            $check = Db::table('node__' . $value)->where('entity_id', $id)->where('langcode', $code ?? $this->target[0])->exists();

            // 没有翻译版本 把内容放进结果里
            if (!$check) {
                $list[$value] = Db::table('node__' . $value)->where('entity_id', $id)->where('langcode', $this->source)->value($value);
            }
        }

        // 过滤空字符串 返回结果
        return array_filter($list);
    }

    /**
     * 为页面设置翻译后的内容
     * 
     * @param  int    $id   页面id
     * @param  array  $html 页面每个字段的翻译结果
     * @param  string $code 语言代码
     * @return array
     */
    private function setPageContent(int $id, array $html, string $code): ?array
    {
        // 获取翻译前的页面内容
        $new = $old = $this->getPageContent($id, $code);

        if (!$new) return [];

        // 判断字段数量是否一致
        if ($html == $this->replace[3] && $old[0] == $this->replace[3]) return null;

        // 把翻以前的数据用翻译后的数据替换
        foreach ($new as $key => $value) {
            $new[$key] = array_splice($html, 0, 1)[0];
        }

        // 设置页面字段并返回翻译了的字段名称
        return $this->setPageFieldContent($id, $new, $code);
    }

    /**
     * 设置页面字段
     * 
     * @param  int    $id   页面id
     * @param  array  $list 字段数据列表
     * @param  string $code 语言代码
     * @return array
     */
    private function setPageFieldContent(int $id, array $list, $code): array
    {
        // 循环每个字段并设置内容
        foreach ($list as $file => $html) {
            switch ($file) {
                case 'title':
                    $data = Db::table('nodes')->where('id', $id)->first();

                    Db::table('node_translations')->insert([
                        'entity_id'     => $id,
                        'mold_id'       => $data->mold_id,
                        'title'         => $html,
                        'view'          => $data->view,
                        'is_red'        => $data->is_red,
                        'is_green'      => $data->is_green,
                        'is_blue'       => $data->is_blue,
                        'langcode'      => $code,
                        'created_at'    => date('Y-m-d H:i:s')
                    ]);
                    break;

                default:
                    Db::table('node__' . $file)->insert([
                        'entity_id'     => $id,
                        $file           => $html,
                        'langcode'      => $code,
                        'created_at'    => date('Y-m-d H:i:s', time())
                    ]);
                    break;
            }
        }

        // 返回修改的字段
        return array_keys($list);
    }

    /**
     * 获取需要复制的模板文件的路径
     * 
     * @return array
     */
    private function getTplFilePath(): array
    {
        $dirs = [
            $this->tplPath . ($this->source == 'en' ? '' : $this->source . '/') . 'message/form/',
            // $this->tplPath . 'specs/',
            $this->tplPath . ($this->source == 'en' ? '' : $this->source . '/')
        ];

        $files = [];

        foreach ($dirs as $dir) {
            $list = array_slice(scandir($dir), 2);
            foreach ($list as $key => $file) {
                $list[$key] = $dir . $file;
                if (is_dir($list[$key])) unset($list[$key]);
            }
            $files = array_merge($files, array_values($list));
        }

        if (($i = array_search($this->tplPath . ($this->source == 'en' ? '' : $this->source . '/') . 'google-sitemap.twig', $files)) !== false) {
            unset($files[$i]);
        }

        return array_values($files);
    }

    /**
     * 翻译后的模板文件写入内容
     * 
     * @param  string $path 文件路径
     * @param  string $html 文件内容
     * @return void
     */
    private function setTplFileContent(string $path, string $html): void
    {
        // 路径信息
        $pathinfo = pathinfo($path);

        // 创建不存在的路径
        if (!is_dir($pathinfo['dirname'])) mkdir($pathinfo['dirname'], 0755, true);

        // 创建文件 写入文件
        if (touch($path)) file_put_contents($path, $html);
    }

    /**
     * 代码转换
     * 
     * @param  string       $code 被转换的代码
     * @param  bool|boolean $type true表示从后台代码转换到翻译平台代码 false相反
     * @return string
     */
    private function code(string $code, bool $type = true) : string
    {
        if ($type) {
            return $this->code[$code] ?? $code;
        } else {
            foreach ($this->code as $key => $value) {
                if ($code == $value) {
                    return $key;
                }
            }
            return $code;
        }
    }
}