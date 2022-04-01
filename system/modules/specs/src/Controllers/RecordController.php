<?php

namespace Specs\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use July\Node\Node;
use July\Node\NodeField;
use Specs\Engine;
use Specs\Spec;

class RecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Specs\Spec  $spec
     * @return \Illuminate\Http\Response
     */
    public function index(Spec $spec)
    {
        $images = [];
        $records = [];
        foreach (DB::table($spec->getRecordsTable())->orderByDesc('id')->get() as $record) {
            $record = (array) $record;
            $record['image_invalid'] = true;
            if ($image = $record['image'] ?? '') {
                $record['image_invalid'] = $images[$image] ?? $images[$image] = !is_file(public_path(ltrim($image, '\\/')));
            }
            $records[] = $record;
        }

        $data = [
            'spec_id' => $spec->getKey(),
            'records' => $records,
            'fields' => $spec->getFields()->all(),
            'template' => $spec->getRecordTemplate(),
        ];

        return view('specs::records.index', $data);
    }

    /**
     * 新建或更新规格数据
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Specs\Spec  $spec
     * @return \Illuminate\Http\Response
     */
    public function upsert(Request $request, Spec $spec)
    {
        $records = $spec->upsertRecords(array_reverse($request->input('records')));

        return response($records);
    }

    /**
     * 删除或清空规格数据
     *
     * @param  \Specs\Spec  $spec
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Spec $spec)
    {
        if (!empty($records = $request->input('records'))) {
            DB::table($spec->getRecordsTable())->whereIn('id', $records)->delete();
        }

        return response('');
    }

    /**
     * 检索规格
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Specs\Spec  $spec
     * @param  string|int  $recordId
     * @return \Illuminate\Http\Response
     */
    public function show(Spec $spec, $recordId)
    {
        $relatedSpec = NodeField::find('related_spec')->getValueModel()
            ->newQuery()->where('related_spec', $spec->getKey())->first();

        return html_compress(app('twig')->render('specs/record.twig', [
            'relatedNode' => Node::find($relatedSpec->entity_id),
            'spec' => $spec,
            'record' => $spec->getRecord($recordId),
        ]));
    }

    /**
     * 检索规格数据
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $keywords = urldecode($request->input('keywords'));

        $data = [
            'results' => Engine::make($request)->search($keywords),
            'keywords' => $keywords,
            'title' => 'Search',
            'meta_title' => 'Search Result',
            'meta_keywords' => 'Search',
            'meta_description' => 'Search Result',
        ];

        return html_compress(app('twig')->render('specs/search.twig', $data));
    }

    /**
     * 获取规格数据
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fetch(Request $request)
    {
        return response(Engine::make($request)->search());
    }

    /**
     * 检查主键是否重复
     *
     * @param  string|int  $id
     * @return \Illuminate\Http\Response
     */
    public function exists($id)
    {
        return response([
            'exists' => !empty(Spec::find($id)),
        ]);
    }

    /**
     * 规格列表
     *
     * @param  \Specs\Spec  $spec
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function list(Spec $spec, Request $request)
    {
        $type = config('specList.model', 'static');

        // 用JS处理数据
        if ($type === 'static') {
            if ($spec->getKey()) {
                // 查看一个规格的列表
                return $this->staticSpec($spec, $request);
            } else {
                // 查看全部规格的列表
                return $this->staticSpecs($spec, $request);
            }
        }

        // 用PHP处理数据
        if ($type === 'dynamic') {
            if ($spec->getKey()) {
                // 查看一个规格的列表
                return $this->dynamicSpec($spec, $request);
            } else {
                // 查看全部规格的列表
                return $this->dynamicSpecs($spec, $request);
            }
        }
    }

    // PHP处理数据时获取数据的接口 暂时不用
    public function getlist(Request $request)
    {
        $data = $request->all();

        $search = isset($data['search']) ? $data['search'] : null;
        $screen = isset($data['screen']) ? $data['screen'] : null;
        $sort = isset($data['sort']) ? $data['sort'] : null;
        $page = isset($data['page']) ? $data['page'] : null;

        // $list = Db::table

        var_dump($data);
    }

    // 查看一个规格的列表
    public function staticSpec(Spec $spec, Request $request)
    {
        $specName = $spec->getKey();

        // 获取当前规格的可检索字段
        $search = Engine::make()->specs($specName)->resolveSpecFields()[$spec->getKey()]['searchable'];

        // 对可分组的字段配置筛选信息
        $screen = [];

        // 定义一个字段的筛选组 manufacturer是字段 ‘$data[] = 1’表示向数组$data里添加一个下标
        $screen[] = [
            // 筛选项前面的名字 必填
            'name'          => 'manufacturer',

            // 筛选的字段 必填
            'field'         => 'manufacturer',

            // 筛选的类型 见文档 必填
            'type'          => 1,

            // 默认值 见文档 选填
            // 'default'       => '',

            // 组件的配置 见文档 选填
            // 'config'        => [],

            // 组件的配置 见文档 选填
            // 'configGroup'    => [],
        ];

        // 定义第二个字段的筛选组
        $screen[] = [
            'name'          => 'field1',
            'field'         => 'field1',
            'type'          => 5,
            'config'        => ['multiple' => false]
        ];

        $screen[] = [
            'name'          => 'field2',
            'field'         => 'field2',
            'type'          => 3,
            'default'       => [10, 50],
            'config'        => [
                'range'     => true,
                'max'       => 100,
                'min' => 20
            ]
        ];
        $screen[] = [
            'name'          => 'time',
            'field'         => 'time',
            'type'          => 4
        ];

        // 定义表格的配置 下面分别是使用表格和不使用表格的配置 二选一

        // 这是不用表格的配置 把状态设置为false 不需要其他值
        $table = [
            'status'    => false
        ];

        // 这时用表格的配置 状态默认true 所以可以不传 传其他配置即可
        $table = [
            // 表格每一列的信息的集合 二维数组
            'column'    => [
                // 第一列的信息
                [
                    // 第一列用的字段
                    'field'     => 'id',

                    // 表头名字 默认用字段名字
                    'title'     => 'id',

                    // 排序 需要这一列排序传true 不需要不传或传false 默认不排序
                    'sortable'  => true,

                    // 列表是有默认排序的 传true表示用这个字段排序
                    'sortableDefaultField'  => true,

                    // 默认排序的排序方式 asc正序 desc倒序 其他非法值会被当做正序
                    'sortableDefaultMode'   => 'asc'
                ],

                // 第二列的信息
                [
                    'field'     => 'manufacturer',
                    'title'     => 'Manufacturer',
                    'sortable'  => true
                ],
                [
                    'field'     => 'manufacture_oe',
                    'title'     => 'Manufacture #',
                    'sortable'  => true
                ],
                [
                    'field'     => 'inborn_filter_oe',
                    'title'     => 'Inborn Filter #',
                    'sortable'  => true
                ],
                [
                    'field'     => 'field1'
                ],
                [
                    'field'     => 'field2'
                ],
                [
                    'field'     => 'time'
                ]
            ],

            // 表格组件的配置信息
            'config'    => [

            ]
        ];

        // 定义列表的html 下面分别是使用列表和不使用列表的情况 二选一

        // 这时不用列表的情况 把变量定义为null
        $listItem = '';

        // 这是用列表的情况
        $listItem = '
            <p>id: { id }</p>
            <p>manufacturer: { manufacturer }</p>
            <p>Manufacture #: { manufacture_oe }</p>
            <p>Inborn Filter #: { inborn_filter_oe }</p>
            <p>field1: { field1 }</p>
            <p>field2: { field2 }</p>
            <p>time: { time }</p>
            <p>Category: { spec }</p>
            <p>id: { id }</p>
        ';

        // 获取当前规格的数据
        $data = Engine::make()->specs($spec->getKey())->search()[$spec->getKey()]['records'];

        // 遍历当前规格的数据 数据的id是字符串 转int
        foreach ($data as $key => $value) $data[$key]['id'] = intval($value['id']);

        // 把所有需要渲染视图的数据放在一起
        $data = [
            // 数据列表
            'list'          => $data,

            // 表格的配置信息
            'table'         => $table,

            // 列表的html
            'listItem'      => $listItem,

            // 后台对表格的配置
            'config'        => $this->getListConfig('static')
        ];

        // 把允许搜索的字段放进来渲染视图
        $data['config']['search']['field'] = $search;

        // 把上面定义的筛选项信息放进来渲染视图
        $data['config']['screen']['list'] = $screen;

        // 把$data渲染视图并获取渲染后的html代码 返回给框架 框架会将代码输出到浏览器
        return html_compress(app('twig')->render('specs/list-static.twig', $data));
    }

    // 查看全部规格的列表
    public function staticSpecs(Spec $spec, Request $request)
    {
        // 获取第一个规格的可检索字段 查看全部规格的列表时字段用第一个规格的字段 务必保证所有规格的字段相同
        $search = array_reverse(array_values(Engine::make()->specs()->resolveSpecFields()))[0]['searchable'];

        // 对可分组的字段配置筛选信息
        $screen = [];

        // 定义一个字段的筛选组 manufacturer是字段 ‘$data[] = 1’表示向数组$data里添加一个下标
        $screen[] = [
            // 筛选项前面的名字 必填
            'name'          => 'manufacturer',

            // 筛选的字段 必填
            'field'         => 'manufacturer',

            // 筛选的类型 见文档 必填
            'type'          => 1,

            // 默认值 见文档 选填
            // 'default'       => '',

            // 组件的配置 见文档 选填
            // 'config'        => [],

            // 组件的配置 见文档 选填
            // 'configGroup'    => [],
        ];

        // 定义第二个字段的筛选组
        $screen[] = [
            'name'          => 'field1',
            'field'         => 'field1',
            'type'          => 5,
            'config'        => ['multiple' => false]
        ];

        $screen[] = [
            'name'          => 'field2',
            'field'         => 'field2',
            'type'          => 3,
            // 'default'       => [10, 50],
            'config'        => [
                'range'     => true,
                'max'       => 100,
                'min'       => 20
            ]
        ];
        $screen[] = [
            'name'          => 'time',
            'field'         => 'time',
            'type'          => 4,
            'default'       => '1650124811',
            'config'        => [
                'type'      => 'week'
            ]
        ];

        // 向数组的开头添加一个下标 因为是多个规格 所以添加了对规格的筛选 并且放在了最前面 不需要可以注释
        array_unshift($screen, [
            'name'          => 'spec',
            'field'         => 'spec',
            'type'          => 2,
            'default'       => ['develop'],
            'config'        => ['border' => true],
            'configGroup'   => ['fill' => 'yellow']
        ]);

        // 定义表格的配置 下面分别是使用表格和不使用表格的配置 二选一

        // 这是不用表格的配置 把状态设置为false 不需要其他值
        $table = [
            'status'    => false
        ];

        // 这是用表格的配置 状态默认true 所以可以不传 传其他配置即可
        $table = [
            // 表格每一列的信息的集合 二维数组
            'column'    => [
                // 第一列的信息
                [
                    // 第一列用的字段
                    'field'     => 'id',

                    // 表头名字 默认用字段名字
                    'title'     => 'id',

                    // 排序 需要这一列排序传true 不需要不传或传false 默认不排序
                    'sortable'  => true,

                    // 列表是有默认排序的 传true表示用这个字段排序
                    'sortableDefaultField'  => true,

                    // 默认排序的排序方式 asc正序 desc倒序 其他非法值会被当做正序
                    'sortableDefaultMode'   => 'asc'
                ],

                // 第二列的信息
                [
                    'field'     => 'manufacturer',
                    'title'     => 'Manufacturer',
                    'sortable'  => true
                ],
                [
                    'field'     => 'manufacture_oe',
                    'title'     => 'Manufacture #',
                    'sortable'  => true
                ],
                [
                    'field'     => 'inborn_filter_oe',
                    'title'     => 'Inborn Filter #',
                    'sortable'  => true
                ],
                [
                    'field'     => 'field1'
                ],
                [
                    'field'     => 'field2'
                ],
                [
                    'field'     => 'time'
                ],
                [
                    'field'     => 'spec',
                    'title'     => 'Category'
                ]
            ],

            // 表格组件的配置信息
            'config'    => [

            ]
        ];

        // 定义列表的html 下面分别是使用列表和不使用列表的情况 二选一

        // 这时不用列表的情况 把变量定义为''
        $listItem = '';

        // 这是用列表的情况
        $listItem = '
            <p>id: { id }</p>
            <p>manufacturer: { manufacturer }</p>
            <p>Manufacture #: { manufacture_oe }</p>
            <p>Inborn Filter #: { inborn_filter_oe }</p>
            <p>field1: { field1 }</p>
            <p>field2: { field2 }</p>
            <p>time: { time }</p>
            <p>Category: { spec }</p>
            <p>id: { id }</p>
        ';

        // 获取全部规格的信息和数据
        $data = Engine::make($request)->search();

        // 定义一个空数组 用来存放全部规格的数据
        $list = [];

        // 遍历全部规格
        foreach ($data as $key => $value) {

            // 遍历每个规格的数据
            foreach ($value['records'] as $k => $val) {
                // 给每条数据添加一个下标 表示这条数据的规格名字 下标为spec
                $value['records'][$k]['spec'] = $value['attributes']['id'];

                // 数据的id是字符串 转int
                $value['records'][$k]['id'] = intval($val['id']);
            }

            // 把处理好的数据放进list里
            $list = array_merge($list, $value['records']);
        }

        // 把所有需要渲染视图的数据放在一起
        $data = [
            // 数据列表
            'list'          => $list,

            // 表格的配置信息
            'table'         => $table,

            // 列表的html
            'listItem'      => $listItem,

            // 后台对表格的配置
            'config'        => $this->getListConfig('static')
        ];

        // 把允许搜索的字段放进来渲染视图
        $data['config']['search']['field'] = $search;

        // 把上面定义的筛选项信息放进来渲染视图
        $data['config']['screen']['list'] = $screen;

        // 把$data渲染视图并获取渲染后的html代码 返回给框架 框架会将代码输出到浏览器
        return html_compress(app('twig')->render('specs/list-static.twig', $data));
    }

    public function dynamicSpec(Spec $spec, Request $request)
    {
        $fields = Engine::make()->specs($spec->getKey())->resolveSpecFields()[$spec->getKey()];
        foreach ($fields['groupable'] as $key => $value) {
            if ($key == 0) $fields['groupable'][$key] = ['name' => $value, 'field' => $value, 'type' => 1];
            if ($key == 1) $fields['groupable'][$key] = ['name' => $value, 'field' => $value, 'type' => 5, 'config' => ['multiple' => false]];
            if ($key == 2) $fields['groupable'][$key] = ['name' => $value, 'field' => $value, 'type' => 3, 'default' => [10, 50], 'config' => ['range' => true, 'max' => 100, 'min' => 20]];
            if ($key == 3) $fields['groupable'][$key] = ['name' => $value, 'field' => $value, 'type' => 4];
        }
        $data = Engine::make()->specs($spec->getKey())->search()[$spec->getKey()];
        foreach ($data['records'] as $key => $value) $data['records'][$key]['id'] = intval($value['id']);
        $data = [
            'attr'          => $data['attributes'],
            'groupable'     => $fields['groupable'],
            'multiple_spec' => false,
            'spec'          => []
        ];
        return html_compress(app('twig')->render('specs/list-dynamic.twig', $data));
    }

    public function dynamicSpecs(Spec $spec, Request $request)
    {
        $fields = array_reverse(array_values(Engine::make()->specs()->resolveSpecFields()))[0];
        foreach ($fields['groupable'] as $key => $value) {
            if ($key == 0) $fields['groupable'][$key] = ['name' => $value, 'field' => $value, 'type' => 1];
            if ($key == 1) $fields['groupable'][$key] = ['name' => $value, 'field' => $value, 'type' => 5, 'config' => ['multiple' => false]];
            if ($key == 2) $fields['groupable'][$key] = ['name' => $value, 'field' => $value, 'type' => 3, 'default' => [10, 50], 'config' => ['range' => true, 'max' => 100, 'min' => 20]];
            if ($key == 3) $fields['groupable'][$key] = ['name' => $value, 'field' => $value, 'type' => 6];
        }
        array_unshift($fields['groupable'], ['name' => 'spec', 'field' => 'spec', 'type' => 2, 'default' => ['develop'], 'config' => ['border' => true], 'configGroup' => ['fill' => 'yellow']]);
        $data = Engine::make($request)->search();
        $list = [];
        foreach ($data as $key => $value) {
            foreach ($value['records'] as $k => $val) {
                $value['records'][$k]['spec'] = $value['attributes']['id'];
                $value['records'][$k]['id'] = intval($val['id']);
            }
            $list = array_merge($list, $value['records']);
        }

        $data = [
            'attr'          => array_values($data)[0]['attributes'],
            'groupable'     => $fields['groupable'],
            'multiple_spec' => true,
            'spec'          => array_keys($data)
        ];
        return html_compress(app('twig')->render('specs/list-dynamic.twig', $data));
    }

    private function getListConfig($model)
    {
        $data = config('specList', []);
        if ($data['model'] != $model) return [];
        $data = array_merge($data[$model], ['cuttingSymbol' => $data['cuttingSymbol']], ['dataEmptyText' => $data['dataEmptyText']]);

        if (is_null($data['search']['inputConfig']['componentConfig'])) {
            unset($data['search']['inputConfig']['componentConfig']);
        } else {
            $config = explode('|', $data['search']['inputConfig']['componentConfig']);
            $config2 = [];
            foreach ($config as $key => $value) {
                $value = explode(':', $value);
                $config2[$value[0]] = format_value($value[1]);
            }

            unset($data['search']['inputConfig']['componentConfig']);
            $data['search']['inputConfig'] = array_merge($data['search']['inputConfig'], $config2);            
        }

        if (is_null($data['search']['buttonConfig']['componentConfig'])) {
            unset($data['search']['buttonConfig']['componentConfig']);
        } else {
            $config = explode('|', $data['search']['buttonConfig']['componentConfig']);
            $config2 = [];
            foreach ($config as $key => $value) {
                $value = explode(':', $value);
                $config2[$value[0]] = format_value($value[1]);
            }

            unset($data['search']['buttonConfig']['componentConfig']);
            $data['search']['buttonConfig'] = array_merge($data['search']['buttonConfig'], $config2);            
        }

        $data['screen']['type'] = intval($data['screen']['type']);
        foreach ($data['screen']['groupCountType'] as $key => $value) {
            $data['screen']['groupCountType'][$key] = intval($value);
        }

        if (is_null($data['pagination']['componentConfig'])) {
            unset($data['pagination']['componentConfig']);
        } else {
            $config = explode('|', $data['pagination']['componentConfig']);
            $config2 = [];
            foreach ($config as $key => $value) {
                $value = explode(':', $value);
                $config2[$value[0]] = format_value($value[1]);
            }

            unset($data['pagination']['componentConfig']);
            $data['pagination'] = array_merge($data['pagination'], $config2);            
        }

        if (is_null($data['loading']['config']['componentConfig'])) {
            unset($data['loading']['config']['componentConfig']);
        } else {
            $config = explode('|', $data['loading']['config']['componentConfig']);
            $config2 = [];
            foreach ($config as $key => $value) {
                $value = explode(':', $value);
                $config2[$value[0]] = format_value($value[1]);
            }

            unset($data['loading']['config']['componentConfig']);
            $data['loading']['config'] = array_merge($data['loading']['config'], $config2);            
        }

        return $data;
    }
}
