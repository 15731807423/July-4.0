<?php
namespace Translate;

use Illuminate\Http\JsonResponse;

/**
 * 文档翻译
 */
class Azure extends Translate
{
	// 域名
	protected $domain;

    // api
    protected $api = [
        'https://api.vip/api/translate/translate',
        'https://api.vip/api/translate/create',
        'https://api.vip/api/translate/get'
    ];
    // protected $api = [
    //     'https://www.shouqibucuo.com/api/translate/translate',
    //     'https://www.shouqibucuo.com/api/translate/create',
    //     'https://www.shouqibucuo.com/api/translate/get'
    // ];

    // 源语言
    protected $source = 'en';

    // 翻译语言
    protected $target;

    // 一键翻译翻译的页面id
    protected $nodes;

    // 不翻译的字段
    protected $notFields;

    // 不翻译的内容
    protected $notText;

    // 指定翻译的内容
    protected $appoint;

    // 模板路径
    protected $tplPath;

    // 翻译结果
    protected $result;

    // 当前模式是不是直接翻译
    protected $mode;

    // 接口错误或任务错误
    // $result = 'error';

    // 翻译成功 可能部分语言错误
    // $result = [
    //     'de'    => '<p></p>',   // 成功
    //     'es'    => false        // 失败
    // ];

    // 标识 第一个用于切割字段 第二个用于切割页面 第三个用于替换空格 第四个用于代替空页面的数据
    protected $replace = [
        '<div class="translate-field-cutting"></div>',
        '<div class="translate-page-cutting"></div>',
        '<div class="translate-space"></div>',
        '<div class="translate-page-empty"></div>'
    ];

    // 全局缓存
    private $cache = [];

    /**
     * 初始化一批成员属性
     */
	function __construct($result = true)
	{
		$this->domain 		= parse_url(env('APP_URL'))['host'];

        $this->notFields    = eval('return ' . str_replace("\n", '', config('translate.fields')) . ';');
        $this->notText      = eval('return ' . str_replace("\n", '', config('translate.text')) . ';');
        $this->appoint      = eval('return ' . str_replace("\n", '', config('translate.replace')) . ';');

        $this->notFields    = is_array($this->notFields) ? $this->notFields : [];
        $this->notText      = is_array($this->notText) ? $this->notText : [];
        $this->appoint      = is_array($this->appoint) ? $this->appoint : [];

        $this->tplPath      = base_path('../themes/frontend/template/');
        $this->mode         = $result;
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
    public function running(string $status, string $data): JsonResponse
    {
        return response()->json([
            'status'    => null,
            'message'   => $status,
            'data'      => $data
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
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
        if (is_null($html)) return $this->error('没有要翻译的内容');

        // 创建任务并获取翻译的结果
        $this->mode ? parent::translate($html) : parent::create($html);

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
                    $html = $this->result[$this->target[0]];

                    // 翻译失败的处理
                    if (!$html) return $this->error('翻译失败');

                    // 翻译完成后处理数据
                    if ($type == 'page') $result = $this->pageAfter($this->cache['pageContent'], $html);
                    if ($type == 'tpl') $result = $this->tplAfter($html);

                    // 处理失败
                    if (!$result) return $this->error('翻译失败');

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
}