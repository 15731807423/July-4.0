<?php

namespace July\Node\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use July\Node\Catalog;
use July\Node\Node;
use July\Node\NodeType;
use July\Node\NodeField;
use July\Node\NodeIndex;
use July\Node\NodeSet;

class NodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'models' => Node::indexWith(['url'])->all(),
            'context' => [
                'molds' => NodeType::query()->pluck('label', 'id')->all(),
                'catalogs' => Catalog::query()->pluck('label', 'id')->all(),
                'languages' => Lang::getTranslatableLangnames(),
            ],
        ];

        return view('node::node.index', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \July\Node\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function show(Node $node)
    {
        //
    }

    /**
     * 选择类型
     *
     * @return \Illuminate\Http\Response
     */
    public function chooseMold()
    {
        $data = [
            'models' => NodeType::all()->all(),
        ];

        return view('node::node.choose_mold', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \July\Node\NodeType  $nodeType
     * @return \Illuminate\Http\Response
     */
    public function create(NodeType $nodeType)
    {
        // 节点模板数据
        $model = array_merge(Node::template(), $nodeType->getFieldValues());
        $model['langcode'] = langcode('content');
        $model['mold_id'] = $nodeType->getKey();

        // 字段集，按是否全局字段分组
        $fields = $nodeType->getFields()->groupBy(function(NodeField $field) {
            return $field->is_global ? 'global' : 'local';
        });

        $data = [
            'model' => $model,
            'context' => [
                'mold' => $nodeType,
                'global_fields' => $fields->get('global', []),
                'local_fields' => $fields->get('local', []),
                'views' => Node::query()->pluck('view')->filter()->unique()->all(),
                'mode' => 'create',
            ],
            'langcode' => langcode('content'),
        ];

        return view('node::node.create-edit', $data);
    }

    /**
     * 展示编辑或翻译界面
     *
     * @param  \July\Node\Node  $node
     * @param  string|null  $langcode
     * @return \Illuminate\Http\Response
     */
    public function edit(Node $node, string $langcode = null)
    {
        if ($langcode) {
            $node->translateTo($langcode);
        }

        // 字段集，按是否全局字段分组
        $fields = $node->getFields()->groupBy(function(NodeField $field) {
            return $field->is_global ? 'global' : 'local';
        });

        $data = [
            'model' => array_merge($node->gather(), ['langcode' => $node->getLangcode()]),
            'context' => [
                'mold' => $node->mold,
                'global_fields' => $fields->get('global'),
                'local_fields' => $fields->get('local'),
                'views' => Node::query()->pluck('view')->filter()->unique()->all(),
                'mode' => $node->isTranslated() ? 'translate' : 'edit',
            ],
            'langcode' => $node->getLangcode(),
        ];

        return view('node::node.create-edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $node = Node::create($request->all());
        return response([
            'node_id' => $node->getKey(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \July\Node\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Node $node)
    {
        // $langcode = langcode('request') ?? $node->getOriginalLangcode();

        $langcode = langname_by_chinese($request->input('langcode')) ? $request->input('langcode') : null;

        $node->translateTo($langcode);

        if ($node->isTranslated()) {
            $node->update($request->all());
        } else {
            $changed = (array) $request->input('_changed');
            if ($changed) {
                $node->update($request->only($changed));
            }
        }

        return response('');
    }

    /**
     * 内容列表删除 nodes表软删除
     *
     * @param  \July\Node\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function destroy(Node $node)
    {
        $list = Db::table('catalog_node')->where('parent_id', $node->id)->distinct()->pluck('catalog_id')->toArray();
        if (count($list)) return $list;

        $node->delete();

        return response('');
    }

    /**
     * 回收站页面
     */
    public function recovery()
    {
        $data = [
            'models' => Node::onlyTrashed()->get()->toArray(),
            'context' => [
                'molds' => NodeType::query()->pluck('label', 'id')->all(),
                'catalogs' => Catalog::query()->pluck('label', 'id')->all(),
                'languages' => Lang::getTranslatableLangnames(),
            ],
        ];

        return view('node::node.recovery', $data);
    }

    /**
     * 回收站恢复 nodes表取消软删除
     */
    public function recovery_recovery_data(Request $request)
    {
        $id = $request->input('nodes');
        if (count($id) == 0) return response('');

        foreach ($id as $key => $value) {
            $node = Node::onlyTrashed()->find($value);
            $node->restore();
            $node->restoreValue();
        }

        return response('');
    }

    /**
     * 回收站删除 nodes表和其他表永久删除
     */
    public function recovery_delete_data(Request $request)
    {
        $id = $request->input('nodes');

        Node::onlyTrashed()->whereIn('id', $id)->forceDelete();

        $fields = Db::table('node_fields')->where('id', '<>', 'url')->pluck('id')->toArray();
        foreach ($fields as $key => $value) $fields[$key] = 'node__' . $value;
        $fields[] = 'node_translations';
        $fields[] = 'entity_path_aliases';
        foreach ($fields as $field) {
            Db::table($field)->whereIn('entity_id', $id)->delete();
        }

        return response('');
    }

    /**
     * 选择语言
     *
     * @param  \July\Node\Node  $node
     * @return \Illuminate\Http\Response
     */
    public function chooseLanguage(Node $node)
    {
        if (! config('lang.multiple')) {
            abort(404);
        }

        return view('languages', [
            'models' => Node::indexWith(['url'])->all(),
            'original_langcode' => $node->getOriginalLangcode(),
            'languages' => Lang::getTranslatableLangnames(),
            'content_id' => $node->getKey(),
            'edit_route' => 'nodes.edit',
            'translate_route' => 'nodes.translate',
        ]);
    }

    /**
     * 渲染内容
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render(Request $request, array $ids = [])
    {
        if ($ids || (!$ids && $ids = $request->input('nodes'))) {
            // 筛选掉还没有触发定时器的页面
            // $future = DB::table('nodes')
            // ->leftJoin('node__timeout', 'nodes.id', '=', 'node__timeout.entity_id')
            // ->where('node__timeout.timeout', '>', time())
            // ->where('node__timeout.langcode', config('lang.frontend'))
            // ->pluck('nodes.id')
            // ->toArray();

            // $ids = array_diff($ids, $future);

            $nodes = NodeSet::fetch($ids);
        } else {
            // 不传参的时候不是更新全部而是报错
            // $nodes = NodeSet::fetchAll();
            abort(500);
        }

        $frontendLangcode = langcode('frontend');

        // 多语言生成
        $langs = config('lang.multiple') ? Lang::getAccessibleLangcodes() : [];

        // 过滤掉默认语言
        $index = array_search(config('lang.frontend'), $langs);

        if ($index !== false) {
            unset($langs[$index]);
            $langs = array_values($langs);
        }

        /** @var \Twig\Environment */
        $twig = app('twig');

        $success = [];
        foreach ($nodes as $node) {
            $result = [];

            try {
                $node->translateTo($frontendLangcode)->render($twig);
            } catch (\Throwable $th) {
                $result['_default'] = false;

                Log::error($th->getMessage());
            }

            if ($langs) {
                foreach ($langs as $langcode) {
                    try {
                        $node->translateTo($langcode)->render($twig, $langcode);
                        $result[$langcode] = true;
                    } catch (\Throwable $th) {
                        $result[$langcode] = false;

                        Log::error($th->getMessage());
                    }
                }
            }

            $success[$node->url] = $result;
        }

        return response($success);
    }

    /**
     * 检索关键词
     *
     * @return string
     */
    public function search(Request $request)
    {
        $multiple = config('lang.multiple');
        $nodes = NodeSet::fetchAll()->keyBy('id');
        $lang = $multiple ? $request->input('lang', langcode('frontend')) : langcode('frontend');

        $results = NodeIndex::search($request->input('keywords'), $lang);
        $results['lang'] = $lang;
        $results['title'] = 'Search';
        $results['meta_title'] = 'Search Result';
        $results['meta_keywords'] = 'Search';
        $results['meta_description'] = 'Search Result';

        foreach ($results['results'] as &$result) {
            $result['node'] = $nodes->get($result['node_id'])->translateTo($lang);
        }

        $tpl = $lang == langcode('frontend') ? 'search.twig' : $lang . '/search.twig';

        if ($multiple) {
            if ($lang) {
                config()->set('lang.output', $lang);
            }
            config()->set('lang.rendering', $lang ?? $this->getLangcode());
        }

        return app('twig')->render($tpl, $results);
    }

    /**
     * 查找无效链接
     *
     * @return \Illuminate\View\View
     */
    public function findInvalidLinks()
    {
        $invalidLinks = [];
        foreach (NodeSet::fetchAll() as $node) {
            $invalidLinks = array_merge($invalidLinks, $node->findInvalidLinks());
        }

        return view('node::node.invalid_links', [
            'invalidLinks' => $invalidLinks,
        ]);
    }

    /**
     * 定时任务
     * 生成HTML、清缓存、谷歌地图、索引
     * 执行这个函数把所有功能调用一遍，定时请求这个函数
     */
    public function timeout()
    {
        $request = new \Illuminate\Http\Request;
        $ids = Db::table('nodes')->pluck('id')->toArray();
        $this->render($request, $ids);

        foreach (config('app.actions') as $key => $value) {
            $data = new $value;
            $data($request);
        }
    }
}
