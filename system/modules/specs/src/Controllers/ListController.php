<?php

namespace Specs\Controllers;

use Specs\Spec;
use Specs\Twig;
use Specs\SpecList;
use Specs\Engine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 前台规格列表页面
 */
class ListController extends Controller
{
    private $model;

    /**
     * 构造函数 允许自定义配置信息 定义的配置信息会覆盖后台的配置信息 未定的配置信息不变
     * 
     * @param  array $config 使用的配置信息 会覆盖后台的配置
     */
    function __construct(array $config = [])
    {
        // 初始化一批成员属性
        $this->model = new SpecList();

        // 设置后台的配置信息 用传进来的配置信息覆盖后台的配置信息
        $this->model->setConfig($config);

        // 设置要显示的规格
        $this->model->specs = Spec::all()->map(function(Spec $spec) {
            return $spec->getKey();
        })->all();

        // 显示多个规格时使用的配置
        $this->model->attrSpec = Spec::find($this->model->config['specAll']['specConfig'] ?: $this->model->specs[0]);
    }

    /**
     * 设置显示的规格
     * 
     * @param  string|array 字符串表示规格的名字 数组表示多个规格名字的集合
     * @return this
     */
    public function setSpecs($specs = null)
    {
        // 如果是字符串 并且这个规格确实存在 表示只有这一个规格
        if (is_string($specs) && in_array($specs, $this->model->specs)) {
            $this->model->specs = [$specs];
        }

        // 如果是数组 获取数组里合法的规格名字（取交集）
        elseif (is_array($specs)) {
            $this->model->specs = array_intersect($specs, $this->model->specs) ?: $this->model->specs;
        }

        if (count($this->model->specs) == 1) {
            $this->model->attrSpec = Spec::find($this->model->specs[0]);
        }

        return $this;
    }

    /**
     * 获取规格功能的html
     * 
     * @return string
     */
    public function tplList()
    {
        // 根据模式调用对应的函数
        switch ($this->model->config['model']) {
            case 'static':
                return $this->model->staticSpec();
                break;

            case 'dynamic':
                return $this->model->dynamicSpec();
                break;
            
            default:
                return '';
                break;
        }
    }

    /**
     * 规格列表 用规格的名称和url里的关键词获取html
     *
     * @param  \Specs\Spec                  $spec       规格信息
     * @return string
     */
    public function list(Spec $spec): string
    {
        if ($spec->getKey()) $this->model->specs = [$spec->getKey()];

        $this->model->setConfig([
            // 'specList.model' => 'dynamic',
            'specList.cuttingSymbol' => ',',
            'specList.dataEmptyText' => 'specList.dataEmptyText',
            // 'specList.sortCaseSensitive' => true,

            'specList.search.default' => '',
            'specList.search.class' => 'specList.search.class',
            'specList.search.status' => true,
            // 'specList.search.inputConfig.onInput' => true,
            'specList.search.inputConfig.onChange' => true,
            'specList.search.inputConfig.class' => 'specList.search.inputConfig.class',
            'specList.search.inputConfig.componentConfig' => 'maxlength:10|showWordLimit:true|placeholder:a b c|clearable:true|showPassword:true|size:default',
            'specList.search.buttonConfig.status' => true,
            'specList.search.buttonConfig.text' => 'specList.search.buttonConfig.text',
            'specList.search.buttonConfig.class' => 'specList.search.buttonConfig.class',
            'specList.search.buttonConfig.componentConfig' => 'size:small|type:success|plain:true|text:true',

            'specList.screen.status' => true,
            'specList.screen.userStatus' => true,
            'specList.screen.countStatus' => true,
            'specList.screen.type' => 4,
            // 'specList.screen.nullHidden' => true,

            'specList.selector.class' => 'specList.selector.class',
            'specList.selector.list.table.text' => 'specList.selector.list.table.text',
            'specList.selector.list.list.text' => 'specList.selector.list.list.text',
            'specList.selector.config.componentConfig' => 'size:large|disabled:true',

            'specList.pagination.class' => 'specList.pagination.class',
            'specList.pagination.pageSize' => 5,
            'specList.pagination.currentPage' => 2,
            'specList.pagination.componentConfig' => 'small:true|background:true|layout:total, sizes, prev, pager, next, jumper',

            'specList.loading.status' => true,

            'specList.specAll.status' => true,
            'specList.specAll.title' => 'specList.specAll.title',
            'specList.specAll.order' => 2,
            'specList.specAll.sortable' => true,
            'specList.specAll.searchable' => true,
            'specList.specAll.screenable' => true,

            'spec' => [
                'manufacturer' => [
                    'is_groupable' => true,
                    'screen_type' => 2
                ],
                'manufacture_oe' => [
                    'is_groupable' => true,
                    'screen_type' => 2
                ]
            ]
        ]);

        return $this->tplList();
    }

    /**
     * 获取PHP处理时页面数据的API
     *
     * @param  \Specs\Spec                  $spec       规格信息
     * @param  \Illuminate\Http\Request     $request    请求信息
     * @return string
     */
    public function getlist(Request $request): array
    {
        $specs = $request->input('specs', null);
        $configUser = $request->input('configUser', '');
        $page = $request->input('page', [1, 10]);
        $sort = $request->input('sort', null);
        $configUser = json_decode($configUser, true) ?: [];
        $this->model->setConfig($configUser);

        // 配置信息
        $config = $this->model->dynamicData($this->model->attrSpec)['config'];

        $queries = collect();
        $screenList = [];

        foreach ($specs as $key => $value) {
            $data = $this->model->getTableData(Spec::find($value), $config, $request);
            $queries->push($data[0]);

            if ($screenList) {
                foreach ($screenList as $field => $list) {
                    $screenList[$field] = $this->model->screenMerge($list, $data[1][$field]);
                }
            } else {
                $screenList = $data[1];
            }
        }

        $list = $queries->shift();

        //循环剩下的表添加union
        $queries->each(function($item, $key) use ($list) {
            $list->unionAll($item);
        });

        // 排序
        if ($sort) {
            if ($config['sortCaseSensitive']) {
                $list->orderByRaw($sort['prop'] . ' ' . $sort['order']);
            } else {
                $list->orderByRaw('lower(' . $sort['prop'] . ')' . ' ' . $sort['order']);
            }
        }

        // $list->dd();

        $count = $list->count();

        $list = $list->offset(($page[0] - 1) * $page[1])->limit($page[1])->get()->toArray();

        return ['status' => 1, 'data' => ['screen' => $screenList, 'list' => $list, 'count' => $count]];
    }
}
