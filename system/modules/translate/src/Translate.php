<?php
namespace Translate;

use App\EntityField\FieldTypes\Html;
use App\EntityField\FieldTypes\Input;
use App\EntityField\FieldTypes\Text;
use App\Support\Settings\Translate as TranslateSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * 翻译
 */
class Translate
{
    public const TOOL = 'alibabacloud';

    private const BATCH_TASK_ENVELOPE = '__july_batch_v1';

    private const TPL_TASK_ENVELOPE = '__july_tpl_v1';

    private const NON_TRANSLATABLE_FIELDS = [
        'url',
        'meta_canonical',
        'image_src',
        'related_spec',
        'timeout',
    ];

    // 域名
    private $domain;

    // api
    // private $api = [
    //     'https://wangke006.vip/api/translate/translate',
    //     'https://wangke006.vip/api/translate/create',
    //     'https://wangke006.vip/api/translate/get'
    // ];
    private $api = [
        'https://wangke006.top/api/v2/translate/translate',
        'https://wangke006.top/api/v2/translate/create',
        'https://wangke006.top/api/v2/translate/get'
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

    // 中转站是否返回失败
    private bool $failed = false;

    // 翻译结果在本地处理失败时的稳定提示
    private ?string $processingError = null;

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

        $this->tool         = self::normalizeTool(config('translate.tool'));

        $code = json_decode(config('translate.code'), true);
        $code = is_array($code) ? $code : [];
        $this->code = is_array($code[$this->tool] ?? null)
            ? $code[$this->tool]
            : (count(array_filter($code, 'is_string')) === count($code) ? $code : []);
    }

    public static function normalizeTool(?string $tool): string
    {
        return self::TOOL;
    }

    public static function batchTargetCodes(?array $available = null, ?string $source = null): array
    {
        $available ??= (array) config('lang.available');
        $source ??= (string) config('lang.translate');
        $targets = [];

        foreach ($available as $code => $info) {
            if ((string) $code === $source
                || !is_array($info)
                || empty($info['translatable'])) {
                continue;
            }
            $targets[] = (string) $code;
        }

        return $targets;
    }

    public static function legacyBatchTargetCode(
        ?array $available = null,
        ?string $source = null
    ): ?string
    {
        return self::batchTargetCodes($available, $source)[0] ?? null;
    }

    public static function isTargetCode(?string $code): bool
    {
        return is_string($code) && in_array($code, self::batchTargetCodes(), true);
    }

    public static function mapLanguageCode(
        string $code,
        array $mapping,
        bool $toPlatform = true
    ): string {
        if ($toPlatform) {
            return is_string($mapping[$code] ?? null) ? $mapping[$code] : $code;
        }

        foreach ($mapping as $local => $platform) {
            if ($code === $platform) {
                return (string) $local;
            }
        }

        return $code;
    }

    /**
     * 设置翻译语言
     * 
     * @param  string $code 翻译语言
     * @return $this
     */
    public function setTo(string $code)
    {
        // 源语言不能和翻译语言一致
        if ($code == $this->source) return $this;

        // 翻译语言必须在后台配置才能翻译
        if (!in_array($code, array_keys(config('lang.available')))) return $this;

        $this->target = $code;

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
        $html = $this->tplBefore();

        // 只有无需翻译的模板副本缺失时，直接补齐，不调用翻译接口。
        if ($html === '' && !empty($this->cache['tplSnapshot']['copies'])) {
            return $this->tplAfter('')
                ? $this->tplSuccess()
                : $this->error($this->processingError ?: '模板处理失败');
        }

        // 翻译
        $result = $this->start($html);

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
            $data = $this->encodeTaskData($this->result['data']);

            return $this->running($this->result['message'], $data);
        }
    }

    public function batchSuccess(string $message)
    {
        return response()->json(['status' => true, 'data' => $message])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
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
        $result = $this->request($this->api[0], [
            'html'          => $html,
            'source'        => $this->code($this->source),
            'target'        => $this->code($this->target),
            'not'           => $this->getNotText(),
            'appoint'       => $this->getAppoint(),
            'domain'        => $this->domain,
            'tool'          => $this->tool,
            'replace'       => $this->replace
        ]);

        $result = self::decodeResponse($result);
        $this->failed = $result['status'] !== true;
        $this->result = $result['status'] === true
            ? $result['data']
            : $result['message'];
    }

    /**
     * 创建翻译任务并获取任务结果
     * 
     * @param  string $html 被翻译的html
     */
    private function create(string $html): void
    {
        $result = $this->request($this->api[1], [
            'html'          => $html,
            'source'        => $this->code($this->source),
            'target'        => $this->code($this->target),
            'not'           => $this->getNotText(),
            'appoint'       => $this->getAppoint(),
            'domain'        => $this->domain,
            'tool'          => $this->tool,
            'replace'       => $this->replace
        ]);

        $result = self::decodeResponse($result);
        $this->failed = $result['status'] !== true;
        $this->result = $result['status'] === true
            ? $this->encodeTaskData($result['data'])
            : $result['message'];
    }

    /**
     * 获取翻译结果
     * 
     * @param  string $data 翻译任务的数据
     */
    private function get(string $data): void
    {
        $data = $this->decodeTaskData($data);
        if ($data === null) {
            $this->result = ['status' => false, 'message' => '翻译任务数据无效'];
            return;
        }

        $result = $this->request($this->api[2], ['data' => $data, 'tool' => $this->tool]);
        $this->result = self::decodeResponse($result);
    }

    public static function decodeResponse(string $response): array
    {
        $result = json_decode($response, true);
        if (
            !is_array($result)
            || !array_key_exists('status', $result)
            || !in_array($result['status'], [true, false, null], true)
        ) {
            return ['status' => false, 'message' => '翻译接口返回格式不正确'];
        }

        if ($result['status'] === true) {
            return is_string($result['data'] ?? null)
                ? ['status' => true, 'data' => $result['data']]
                : ['status' => false, 'message' => '翻译接口返回格式不正确'];
        }

        if ($result['status'] === null) {
            return is_string($result['message'] ?? null) && is_string($result['data'] ?? null)
                ? ['status' => null, 'message' => $result['message'], 'data' => $result['data']]
                : ['status' => false, 'message' => '翻译接口返回格式不正确'];
        }

        return [
            'status' => false,
            'message' => is_string($result['message'] ?? null) && $result['message'] !== ''
                ? $result['message']
                : '翻译接口调用失败',
        ];
    }

    private function request(string $url, array $data): string
    {
        try {
            return Authentication::post($url, $data, $this->domain);
        } catch (\Throwable $exception) {
            return json_encode([
                'status' => false,
                'message' => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * 批量翻译前处理页面数据返回需要翻译的内容
     * 
     * @return string
     */
    private function batchBefore(): string
    {
        $html = [];
        $snapshot = [];

        // 循环每个页面 获取每个页面需要翻译的内容
        foreach ($this->nodes as $id) {
            $data = $this->getPageContent($id);
            if (!$data) continue;

            $snapshot[] = [
                'id' => (int) $id,
                'fields' => array_keys($data),
                'source_hashes' => array_map(function ($value): string {
                    return hash('sha256', (string) $value);
                }, $data),
            ];
            $html[] = implode($this->replace[0], $data);
        }

        $this->cache['batchSnapshot'] = $snapshot;

        return count($html) == 0 ? '' : implode($this->replace[1], $html);
    }

    /**
     * 批量翻译完成后把结果写入对应的语言的数据库中
     * 
     * @param  array $data
     * @return array
     */
    private function batchAfter(string $html): string
    {
        $pages = explode($this->replace[1], $html);
        $snapshot = $this->cache['batchSnapshot'] ?? $this->currentBatchSnapshot();

        if (count($pages) !== count($snapshot)) {
            return '翻译前后页面数量不一致';
        }

        $writes = [];
        foreach ($snapshot as $key => $page) {
            $fields = explode($this->replace[0], $pages[$key]);
            if (count($fields) !== count($page['fields'])) {
                return '翻译前后字段数量不一致';
            }

            $writes[] = [
                'id' => $page['id'],
                'fields' => $page['fields'],
                'source_hashes' => $page['source_hashes'] ?? null,
                'html' => $fields,
            ];
        }

        try {
            DB::transaction(function () use ($writes): void {
                foreach ($writes as $write) {
                    $this->assertSourceContentUnchanged(
                        $write['id'],
                        $write['fields'],
                        $write['source_hashes']
                    );
                    $this->setPageContent(
                        $write['id'],
                        $write['html'],
                        $this->target,
                        $write['fields']
                    );
                }
            });
        } catch (\UnexpectedValueException $exception) {
            return $exception->getMessage();
        } catch (\Throwable $exception) {
            report($exception);
            return '翻译结果写入失败';
        }

        return '翻译成功';
    }

    /**
     * 兼容升级前已经创建的任务：没有字段快照时按当前缺失字段生成一次。
     */
    private function currentBatchSnapshot(): array
    {
        $snapshot = [];

        foreach ($this->nodes as $id) {
            $data = $this->getPageContent($id);
            if (!$data) continue;

            $snapshot[] = [
                'id' => (int) $id,
                'fields' => array_keys($data),
                'source_hashes' => array_map(function ($value): string {
                    return hash('sha256', (string) $value);
                }, $data),
            ];
        }

        return $snapshot;
    }

    private function encodeTaskData(string $data): string
    {
        if (array_key_exists('batchSnapshot', $this->cache)) {
            return $this->encodeBatchTaskData($data);
        }
        if (array_key_exists('tplSnapshot', $this->cache)) {
            return $this->encodeTplTaskData($data);
        }

        return $data;
    }

    private function decodeTaskData(string $data): ?string
    {
        $envelope = json_decode($data, true);
        if (is_array($envelope) && array_key_exists(self::BATCH_TASK_ENVELOPE, $envelope)) {
            return $this->decodeBatchTaskData($data);
        }
        if (is_array($envelope) && array_key_exists(self::TPL_TASK_ENVELOPE, $envelope)) {
            return $this->decodeTplTaskData($data);
        }

        return $data;
    }

    /**
     * 异步任务需要跨请求保留字段顺序。任务数据由浏览器原样传回，签名用于防止字段名被篡改。
     */
    private function encodeBatchTaskData(string $data): string
    {
        if (!array_key_exists('batchSnapshot', $this->cache)) {
            return $data;
        }

        $key = $this->taskSignatureKey();
        if ($key === '') {
            return $data;
        }

        $payload = json_encode([
            'version' => 1,
            'data' => $data,
            'source' => $this->source,
            'target' => $this->target,
            'nodes' => array_values(array_map('intval', $this->nodes)),
            'snapshot' => $this->cache['batchSnapshot'],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (!is_string($payload)) {
            return $data;
        }

        return json_encode([
            self::BATCH_TASK_ENVELOPE => base64_encode($payload),
            'signature' => hash_hmac('sha256', $payload, $key),
        ], JSON_UNESCAPED_SLASHES) ?: $data;
    }

    /**
     * 还原新版异步任务数据；不带新版标记的数据按旧任务原样处理。
     */
    private function decodeBatchTaskData(string $data): ?string
    {
        $envelope = json_decode($data, true);
        if (!is_array($envelope) || !array_key_exists(self::BATCH_TASK_ENVELOPE, $envelope)) {
            return $data;
        }

        $encoded = $envelope[self::BATCH_TASK_ENVELOPE] ?? null;
        $signature = $envelope['signature'] ?? null;
        $key = $this->taskSignatureKey();
        if (!is_string($encoded) || !is_string($signature) || $key === '') {
            return null;
        }

        $payloadJson = base64_decode($encoded, true);
        if (!is_string($payloadJson)
            || !hash_equals(hash_hmac('sha256', $payloadJson, $key), $signature)) {
            return null;
        }

        $payload = json_decode($payloadJson, true);
        if (!$this->isValidBatchTaskPayload($payload)) {
            return null;
        }

        $this->cache['batchSnapshot'] = $payload['snapshot'];

        return $payload['data'];
    }

    private function isValidBatchTaskPayload($payload): bool
    {
        if (!is_array($payload)
            || ($payload['version'] ?? null) !== 1
            || !is_string($payload['data'] ?? null)
            || $payload['data'] === ''
            || ($payload['source'] ?? null) !== $this->source
            || ($payload['target'] ?? null) !== $this->target
            || !is_array($payload['nodes'] ?? null)
            || !is_array($payload['snapshot'] ?? null)
            || !array_is_list($payload['nodes'])
            || !array_is_list($payload['snapshot'])) {
            return false;
        }

        $payloadNodes = $payload['nodes'];
        $currentNodes = array_values(array_map('intval', $this->nodes ?? []));
        if (count($payloadNodes) !== count(array_filter($payloadNodes, 'is_int'))) {
            return false;
        }

        sort($payloadNodes);
        sort($currentNodes);
        if ($payloadNodes !== $currentNodes) {
            return false;
        }

        $snapshotNodes = [];
        foreach ($payload['snapshot'] as $page) {
            if (!is_array($page)
                || !is_int($page['id'] ?? null)
                || $page['id'] < 1
                || !is_array($page['fields'] ?? null)
                || !$page['fields']
                || !array_is_list($page['fields'])
                || count($page['fields']) !== count(array_filter($page['fields'], function ($field): bool {
                    return is_string($field) && $field !== '';
                }))
                || count($page['fields']) !== count(array_unique($page['fields']))) {
                return false;
            }

            if (isset($page['source_hashes'])) {
                if (!is_array($page['source_hashes'])
                    || array_keys($page['source_hashes']) !== $page['fields']
                    || count($page['source_hashes']) !== count(array_filter(
                        $page['source_hashes'],
                        function ($hash): bool {
                            return is_string($hash) && preg_match('/^[a-f0-9]{64}$/', $hash);
                        }
                    ))) {
                    return false;
                }
            }

            $snapshotNodes[] = $page['id'];
        }

        return count($snapshotNodes) === count(array_unique($snapshotNodes))
            && !array_diff($snapshotNodes, $payloadNodes);
    }

    private function encodeTplTaskData(string $data): string
    {
        $key = $this->taskSignatureKey();
        if ($key === '') {
            return $data;
        }

        $payload = json_encode([
            'version' => 1,
            'data' => $data,
            'source' => $this->source,
            'target' => $this->target,
            'snapshot' => $this->cache['tplSnapshot'],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!is_string($payload)) {
            return $data;
        }

        return json_encode([
            self::TPL_TASK_ENVELOPE => base64_encode($payload),
            'signature' => hash_hmac('sha256', $payload, $key),
        ], JSON_UNESCAPED_SLASHES) ?: $data;
    }

    private function decodeTplTaskData(string $data): ?string
    {
        $envelope = json_decode($data, true);
        $encoded = is_array($envelope) ? ($envelope[self::TPL_TASK_ENVELOPE] ?? null) : null;
        $signature = is_array($envelope) ? ($envelope['signature'] ?? null) : null;
        $key = $this->taskSignatureKey();
        if (!is_string($encoded) || !is_string($signature) || $key === '') {
            return null;
        }

        $payloadJson = base64_decode($encoded, true);
        if (!is_string($payloadJson)
            || !hash_equals(hash_hmac('sha256', $payloadJson, $key), $signature)) {
            return null;
        }

        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)
            || ($payload['version'] ?? null) !== 1
            || !is_string($payload['data'] ?? null)
            || $payload['data'] === ''
            || ($payload['source'] ?? null) !== $this->source
            || ($payload['target'] ?? null) !== $this->target
            || !$this->isValidTplSnapshot($payload['snapshot'] ?? null)) {
            return null;
        }

        $this->cache['tplSnapshot'] = $payload['snapshot'];

        return $payload['data'];
    }

    private function isValidTplSnapshot($snapshot): bool
    {
        if (!is_array($snapshot)
            || !is_array($snapshot['files'] ?? null)
            || !array_is_list($snapshot['files'])
            || !is_array($snapshot['copies'] ?? null)
            || !array_is_list($snapshot['copies'])) {
            return false;
        }

        $paths = [];
        foreach (array_merge($snapshot['files'], $snapshot['copies']) as $file) {
            $path = is_array($file) ? ($file['path'] ?? null) : null;
            $hash = is_array($file) ? ($file['hash'] ?? null) : null;
            if (!$this->isSafeRelativeTemplatePath($path)
                || !is_string($hash)
                || !preg_match('/^[a-f0-9]{64}$/', $hash)
                || in_array($path, $paths, true)) {
                return false;
            }
            $paths[] = $path;
        }

        return true;
    }

    private function taskSignatureKey(): string
    {
        $key = (string) config('app.key');
        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);
            if (is_string($decoded)) {
                return $decoded;
            }
        }

        return $key;
    }

    /**
     * 翻译页面前处理页面数据返回需要翻译的内容
     * 
     * @param  array $html
     * @return string
     */
    private function pageBefore(array $html): string
    {
        $excluded = array_merge(self::NON_TRANSLATABLE_FIELDS, $this->getNotFields());
        foreach ($html as $key => $value) {
            if (in_array($key, $excluded, true)) {
                unset($html[$key]);
            }
        }
        if (!$html) {
            return '';
        }

        $fields = [];
        if (!in_array('title', $this->getNotFields(), true)) {
            $fields[] = 'title';
        }
        if (array_diff(array_keys($html), ['title'])) {
            $fields = array_merge($fields, $this->getTranslatableFieldIds());
        }

        // 去掉不需要翻译或不是文字类型的字段
        foreach ($html as $key => $value) {
            if (!in_array($key, $fields, true)) {
                unset($html[$key]);
            }
        }

        return count($html) == 0 ? '' : implode($this->replace[0], $html);
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
        $excluded = array_merge(self::NON_TRANSLATABLE_FIELDS, $this->getNotFields());
        foreach ($old as $key => $value) {
            if (in_array($key, $excluded, true)) {
                unset($old[$key]);
            }
        }
        if (!$old) {
            return null;
        }

        $fields = [];
        if (!in_array('title', $this->getNotFields(), true)) {
            $fields[] = 'title';
        }
        if (array_diff(array_keys($old), ['title'])) {
            $fields = array_merge($fields, $this->getTranslatableFieldIds());
        }

        // 去掉不需要翻译或不是文字类型的字段
        foreach ($old as $key => $value) {
            if (!in_array($key, $fields, true)) {
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
     * @return string
     */
    private function tplBefore(): string
    {
        $html = [];
        $snapshot = [
            'files' => [],
            'copies' => [],
        ];

        foreach ($this->getTplFilePath(true) as $file) {
            $content = file_get_contents($file);
            if (!is_string($content)) {
                continue;
            }

            $snapshot['files'][] = [
                'path' => $this->relativeTemplatePath($file),
                'hash' => hash('sha256', $content),
            ];
            $html[] = $content;
        }

        foreach ($this->getTplCopyFilePath(true) as $file) {
            $content = file_get_contents($file);
            if (!is_string($content)) {
                continue;
            }

            $snapshot['copies'][] = [
                'path' => $this->relativeTemplatePath($file),
                'hash' => hash('sha256', $content),
            ];
        }

        $this->cache['tplSnapshot'] = $snapshot;

        return count($html) == 0 ? '' : implode($this->replace[0], $html);
    }

    /**
     * 翻译模板完成后处理数据
     * 
     * @param  string $html
     * @return bool
     */
    private function tplAfter(string $html): bool
    {
        $snapshot = $this->cache['tplSnapshot'] ?? $this->currentTplSnapshot();
        $translated = $snapshot['files'] ? explode($this->replace[0], $html) : [];
        if (count($translated) !== count($snapshot['files'])) {
            $this->processingError = '翻译前后模板数量不一致';
            return false;
        }

        $targetRoot = $this->targetTemplateRoot();
        $stageRoot = rtrim($targetRoot, '/\\')
            . '.translate-' . bin2hex(random_bytes(8)) . '/';

        try {
            if (!mkdir($stageRoot, 0755, true) && !is_dir($stageRoot)) {
                throw new \RuntimeException('无法创建模板临时目录');
            }

            foreach ($snapshot['files'] as $key => $file) {
                if (is_file($targetRoot . $file['path'])) {
                    continue;
                }
                $this->assertTemplateSourceUnchanged($file);
                $this->writeTemplateFile($stageRoot . $file['path'], $translated[$key]);
            }

            foreach ($snapshot['copies'] as $file) {
                if (is_file($targetRoot . $file['path'])) {
                    continue;
                }
                $content = $this->assertTemplateSourceUnchanged($file);
                $this->writeTemplateFile($stageRoot . $file['path'], $content);
            }

            $this->commitStagedTemplates($stageRoot, $targetRoot);
            return true;
        } catch (\UnexpectedValueException $exception) {
            $this->processingError = $exception->getMessage();
            return false;
        } catch (\Throwable $exception) {
            report($exception);
            $this->processingError = '模板写入失败，现有模板未被覆盖';
            return false;
        } finally {
            $this->removeTemplateDirectory($stageRoot);
        }
    }

    /**
     * 处理后开始翻译
     * 
     * @param  string $html   翻译的内容
     * @param  object|null $before 处理的函数
     * @return JsonResponse|bool
     */
    private function start(string $html, ?object $before = null): JsonResponse|bool
    {
        if ($before) {
            $result = $before();
            if ($result) return $result;
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
        if ($this->failed) {
            return $this->error(is_string($this->result) ? $this->result : '翻译失败');
        }

        // 直接返回结果
        if ($this->mode || $result) {
            // 返回错误信息
            // if (is_string($this->result)) return $this->error($this->result);

            $this->result = $this->linkAddCode($this->result, $this->target);
            $this->result = $this->tplIncludePath($this->result, $this->target);

            switch ($type) {
                case 'batch':
                    // 翻译完成 把每个语言写入对应数据库表 获取写入结果
                    $result = $this->batchAfter($this->result);

                    if ($result !== '翻译成功') {
                        return $this->error($result);
                    }

                    return $this->batchSuccess($result);

                case 'page':
                case 'tpl':
                    // 翻译结果
                    $html = $this->result;

                    $local = $this->target;
                    $tool = $this->code($local, true);
                    $html = str_replace($tool, $local, $html);

                    // 翻译失败的处理
                    if (!$html) return $this->error('翻译失败');

                    // 翻译完成后处理数据
                    if ($type == 'page') $result = $this->pageAfter($this->cache['pageContent'], $html);
                    if ($type == 'tpl') $result = $this->tplAfter($html);

                    // 处理失败
                    if (!$result) {
                        return $this->error($this->processingError ?: '翻译成功但处理失败');
                    }

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
            return $this->notFields[$this->target] ?? [];
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
        if (is_string($this->target)) {
            return TranslateSettings::replacementMapForCode(
                $this->appoint,
                $this->target,
                $this->source,
                (string) config('lang.frontend')
            );
        }

        $list = [];
        foreach ($this->target as $value) {
            $list[$value] = TranslateSettings::replacementMapForCode(
                $this->appoint,
                $value,
                $this->source,
                (string) config('lang.frontend')
            );
        }

        return array_filter($list);
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
        $code = $code ?? $this->target;

        // 结果
        $list = [];

        $fields = $this->getTranslatableFieldIds();

        // 判断title是否存在翻译版本
        $check = Db::table('node_translations')->where('entity_id', $id)->where('langcode', $code)->exists();

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
            $check = Db::table('node__' . $value)->where('entity_id', $id)->where('langcode', $code)->exists();

            // 没有翻译版本 把内容放进结果里
            if (!$check) {
                $list[$value] = Db::table('node__' . $value)->where('entity_id', $id)->where('langcode', $this->source)->value($value);
            }
        }

        // 只过滤真正的空值，字符串 "0" 也是需要翻译的有效内容。
        return array_filter($list, function ($value): bool {
            return $value !== null && $value !== '';
        });
    }

    private function getTranslatableFieldIds(): array
    {
        // 链接、文件、图片、引用和定时字段即使旧配置未排除也不会送去翻译。
        $fields = Db::table('node_fields')
            ->get(['id', 'field_type'])
            ->filter(function ($field): bool {
                $class = $field->field_type ?? null;
                if (!is_string($class)
                    || $class === ''
                    || in_array($field->id, self::NON_TRANSLATABLE_FIELDS, true)) {
                    return false;
                }

                return is_a($class, Input::class, true)
                    || is_a($class, Text::class, true)
                    || is_a($class, Html::class, true);
            })
            ->pluck('id')
            ->toArray();

        // 过滤不翻译的字段
        return array_values(array_diff($fields, $this->getNotFields()));
    }

    /**
     * 为页面设置翻译后的内容
     * 
     * @param  int    $id   页面id
     * @param  array  $html 页面每个字段的翻译结果
     * @param  string $code 语言代码
     * @return array
     */
    private function setPageContent(int $id, array $html, string $code, ?array $fields = null): array
    {
        $fields ??= array_keys($this->getPageContent($id, $code));
        if (count($html) !== count($fields)) {
            throw new \UnexpectedValueException('翻译前后字段数量不一致');
        }

        // 设置页面字段并返回翻译了的字段名称
        return $this->setPageFieldContent($id, array_combine($fields, $html), $code);
    }

    private function assertSourceContentUnchanged(int $id, array $fields, ?array $hashes): void
    {
        if (!$hashes) {
            return;
        }

        foreach ($fields as $field) {
            if ($field === 'title') {
                $query = $this->source === 'en'
                    ? Db::table('nodes')->where('id', $id)
                    : Db::table('node_translations')
                        ->where('entity_id', $id)
                        ->where('langcode', $this->source);
                $value = $query->lockForUpdate()->value('title');
            } else {
                $value = Db::table('node__' . $field)
                    ->where('entity_id', $id)
                    ->where('langcode', $this->source)
                    ->lockForUpdate()
                    ->value($field);
            }

            $expected = $hashes[$field] ?? null;
            $actual = hash('sha256', (string) $value);
            if (!is_string($expected) || !hash_equals($expected, $actual)) {
                throw new \UnexpectedValueException('翻译期间源内容已更新，请重新翻译');
            }
        }
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
        $node = Db::table('nodes')->where('id', $id)->lockForUpdate()->first();
        if (!$node) {
            throw new \RuntimeException('页面不存在');
        }

        $written = [];

        // 循环每个字段并设置内容
        foreach ($list as $file => $html) {
            switch ($file) {
                case 'title':
                    if (Db::table('node_translations')
                        ->where('entity_id', $id)
                        ->where('langcode', $code)
                        ->exists()) {
                        break;
                    }

                    Db::table('node_translations')->insert([
                        'entity_id'     => $id,
                        'mold_id'       => $node->mold_id,
                        'title'         => $html,
                        'view'          => $node->view,
                        'is_red'        => $node->is_red,
                        'is_green'      => $node->is_green,
                        'is_blue'       => $node->is_blue,
                        'langcode'      => $code,
                        'created_at'    => date('Y-m-d H:i:s')
                    ]);
                    $written[] = $file;
                    break;

                default:
                    if (Db::table('node__' . $file)
                        ->where('entity_id', $id)
                        ->where('langcode', $code)
                        ->exists()) {
                        break;
                    }

                    Db::table('node__' . $file)->insert([
                        'entity_id'     => $id,
                        $file           => $html,
                        'langcode'      => $code,
                        'created_at'    => date('Y-m-d H:i:s', time())
                    ]);
                    $written[] = $file;
                    break;
            }
        }

        // 返回修改的字段
        return $written;
    }

    /**
     * 获取需要复制的模板文件的路径
     * 
     * @return array
     */
    private function getTplFilePath(bool $missingOnly = false): array
    {
        $sourceRoot = $this->sourceTemplateRoot();
        $dirs = [
            $sourceRoot . 'message/form/',
            // $this->tplPath . 'specs/',
            $sourceRoot,
        ];

        $files = [];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            $list = array_slice(scandir($dir), 2);
            foreach ($list as $key => $file) {
                $list[$key] = $dir . $file;
                if (is_dir($list[$key])) unset($list[$key]);
            }
            $files = array_merge($files, array_values($list));
        }

        if (($i = array_search($sourceRoot . 'google-sitemap.twig', $files)) !== false) {
            unset($files[$i]);
        }

        if ($missingOnly) {
            $targetRoot = $this->targetTemplateRoot();
            $files = array_filter($files, function ($file) use ($targetRoot): bool {
                return !is_file($targetRoot . $this->relativeTemplatePath($file));
            });
        }

        return array_values($files);
    }

    private function getTplCopyFilePath(bool $missingOnly = false): array
    {
        $directory = $this->sourceTemplateRoot() . 'message/content/';
        if (!is_dir($directory)) {
            return [];
        }

        $files = [];
        foreach (array_slice(scandir($directory), 2) as $file) {
            $path = $directory . $file;
            if (is_file($path)) {
                $files[] = $path;
            }
        }

        if ($missingOnly) {
            $targetRoot = $this->targetTemplateRoot();
            $files = array_filter($files, function ($file) use ($targetRoot): bool {
                return !is_file($targetRoot . $this->relativeTemplatePath($file));
            });
        }

        return array_values($files);
    }

    private function currentTplSnapshot(): array
    {
        $snapshot = ['files' => [], 'copies' => []];

        foreach ($this->getTplFilePath(true) as $file) {
            $content = file_get_contents($file);
            if (is_string($content)) {
                $snapshot['files'][] = [
                    'path' => $this->relativeTemplatePath($file),
                    'hash' => hash('sha256', $content),
                ];
            }
        }
        foreach ($this->getTplCopyFilePath(true) as $file) {
            $content = file_get_contents($file);
            if (is_string($content)) {
                $snapshot['copies'][] = [
                    'path' => $this->relativeTemplatePath($file),
                    'hash' => hash('sha256', $content),
                ];
            }
        }

        return $snapshot;
    }

    private function sourceTemplateRoot(): string
    {
        return rtrim($this->tplPath, '/\\') . '/'
            . ($this->source === 'en' ? '' : $this->source . '/');
    }

    private function targetTemplateRoot(): string
    {
        return rtrim($this->tplPath, '/\\') . '/' . $this->target . '/';
    }

    private function relativeTemplatePath(string $path): string
    {
        $sourceRoot = str_replace('\\', '/', $this->sourceTemplateRoot());
        $path = str_replace('\\', '/', $path);
        if (!str_starts_with($path, $sourceRoot)) {
            throw new \InvalidArgumentException('模板文件不在源目录中');
        }

        $relative = substr($path, strlen($sourceRoot));
        if (!$this->isSafeRelativeTemplatePath($relative)) {
            throw new \InvalidArgumentException('模板相对路径不合法');
        }

        return $relative;
    }

    private function isSafeRelativeTemplatePath($path): bool
    {
        if (!is_string($path)
            || $path === ''
            || str_starts_with($path, '/')
            || str_contains($path, '\\')
            || str_contains($path, "\0")) {
            return false;
        }

        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                return false;
            }
        }

        return true;
    }

    private function assertTemplateSourceUnchanged(array $file): string
    {
        if (!$this->isSafeRelativeTemplatePath($file['path'] ?? null)) {
            throw new \UnexpectedValueException('模板任务路径无效');
        }

        $path = $this->sourceTemplateRoot() . $file['path'];
        $content = is_file($path) ? file_get_contents($path) : false;
        if (!is_string($content)
            || !is_string($file['hash'] ?? null)
            || !hash_equals($file['hash'], hash('sha256', $content))) {
            throw new \UnexpectedValueException('翻译期间源模板已更新，请重新翻译');
        }

        return $content;
    }

    protected function writeTemplateFile(string $path, string $content): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)
            && !mkdir($directory, 0755, true)
            && !is_dir($directory)) {
            throw new \RuntimeException('无法创建模板目录');
        }
        if (file_put_contents($path, $content, LOCK_EX) === false) {
            throw new \RuntimeException('无法写入模板文件');
        }
    }

    private function commitStagedTemplates(string $stageRoot, string $targetRoot): void
    {
        $files = $this->templateFilesRecursively($stageRoot);
        if (!$files) {
            return;
        }

        if (!is_dir($targetRoot)) {
            $parent = dirname(rtrim($targetRoot, '/\\'));
            if (!is_dir($parent) && !mkdir($parent, 0755, true) && !is_dir($parent)) {
                throw new \RuntimeException('无法创建模板目标目录');
            }
            if (!rename(rtrim($stageRoot, '/\\'), rtrim($targetRoot, '/\\'))) {
                throw new \RuntimeException('无法提交模板目录');
            }
            return;
        }

        $lockDirectory = storage_path('framework/cache');
        if (!is_dir($lockDirectory)) {
            mkdir($lockDirectory, 0755, true);
        }
        $lock = fopen($lockDirectory . '/translate-template-'
            . hash('sha256', $targetRoot) . '.lock', 'c+');
        if (!$lock || !flock($lock, LOCK_EX)) {
            if ($lock) fclose($lock);
            throw new \RuntimeException('无法锁定模板目录');
        }

        $moved = [];
        try {
            foreach ($files as $source) {
                $relative = substr(str_replace('\\', '/', $source), strlen(str_replace('\\', '/', $stageRoot)));
                $relative = ltrim($relative, '/');
                if (!$this->isSafeRelativeTemplatePath($relative)) {
                    throw new \RuntimeException('模板提交路径不合法');
                }

                $target = $targetRoot . $relative;
                if (is_file($target)) {
                    continue;
                }

                $directory = dirname($target);
                if (!is_dir($directory)
                    && !mkdir($directory, 0755, true)
                    && !is_dir($directory)) {
                    throw new \RuntimeException('无法创建模板目标目录');
                }

                $this->moveTemplateFile($source, $target);
                $moved[] = $target;
            }
        } catch (\Throwable $exception) {
            foreach (array_reverse($moved) as $path) {
                if (is_file($path)) {
                    unlink($path);
                }
            }
            throw $exception;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    protected function moveTemplateFile(string $source, string $target): void
    {
        if (!rename($source, $target)) {
            throw new \RuntimeException('无法提交模板文件');
        }
    }

    private function templateFilesRecursively(string $directory): array
    {
        if (!is_dir($directory)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);
        return $files;
    }

    private function removeTemplateDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $path) {
            $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
        rmdir($directory);
    }

    /**
     * 代码转换
     * 
     * @param  string       $code 被转换的代码
     * @param  bool|boolean $type true表示从后台代码转换到翻译平台代码 false相反
     * @return string
     */
    private function code(string $code, bool $type = true): string
    {
        return self::mapLanguageCode($code, $this->code, $type);
    }

    /**
     * a标签的链接前加上语言代码
     */
    private function linkAddCode(string $html, string $code): string
    {
        $dom = $this->DOMDocument($html);
        $as = $dom->getElementsByTagName('a');

        $list = [];
        foreach ($as as $a) {
            $href = $a->getAttribute('href');
            
            if (in_array($href, $list)) continue;
            
            if (substr($href, 0, 1) != '/') continue;

            if (substr($href, -5) !== '.html' && stripos($href, '.html#') === false) continue;
            
            $list[$href] = '/' . $code . $href;
        }
        
        foreach ($list as $old => $new) {
            $html = str_replace('href="' . $old . '"', 'href="' . $new . '"', $html);
        }

        return $html;
    }

    /**
     * 模板文件引入文件的路径前加上语言代码
     */
    private function tplIncludePath(string $html, string $code): string
    {
        return str_replace([
            '{% extends "_layout.twig" %}',
            '{% use "_blocks.twig" %}'
        ], [
            '{% extends "' . $code . '/_layout.twig" %}',
            '{% use "' . $code . '/_blocks.twig" %}'
        ], $html);
    }

    /**
     * PHP操作HTML的dom
     * 
     * @return DOMDocument
     */
    private function DOMDocument(string $html): \DOMDocument
    {
        // 构建dom
        $dom = new \DOMDocument();
        $libxml_previous_state = libxml_use_internal_errors(true);
        try {
            $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NONET);
        } finally {
            libxml_use_internal_errors($libxml_previous_state);
        }
        return $dom;
    }
}
