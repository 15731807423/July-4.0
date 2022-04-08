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
    private function staticSpec($spec, $request)
    {
        $data = Engine::make()->specs($spec->getKey())->search()[$spec->getKey()]['records'];

        foreach ($data as $key => $value) $data[$key]['id'] = intval($value['id']);

        $data = $this->staticData($spec, $data);

        // exit(htmlentities(json_encode($data)));

        return html_compress(app('twig')->render('specs/list-static.twig', $data));
    }

    // 查看全部规格的列表
    private function staticSpecs($spec, $request)
    {
        $spec = Spec::all()->map(function(Spec $spec) {
            return $spec;
        })->all()[0];

        $data = Engine::make($request)->search();

        $list = [];

        foreach ($data as $key => $value) {
            foreach ($value['records'] as $k => $val) {
                $value['records'][$k]['spec'] = $value['attributes']['id'];

                $value['records'][$k]['id'] = intval($val['id']);
            }

            $list = array_merge($list, $value['records']);
        }

        $data = $this->staticData($spec, $list);

        // exit(htmlentities(json_encode($data)));

        return html_compress(app('twig')->render('specs/list-static.twig', $data));
    }

    private function staticData($spec, $list)
    {
        $specName = $spec->getKey();

        $fields = $spec->getFields()->values()->all();

        $data = $spec->attributesToArray();

        $hidden = [];
        foreach ($fields as $key => $value) {
            if ($value['is_hiddenable']) $hidden[] = $value['field_id'];
        }

        ['searchable' => $search, 'groupable' => $screen] = Engine::make()->specs($specName)->resolveSpecFields()[$specName];

        $search = array_values(array_diff($search, $hidden));
        $screen = array_values(array_diff($screen, $hidden));

        foreach ($screen as $key => $value) {
            foreach ($fields as $k => $val) {
                if ($val['is_hiddenable']) continue;

                if ($value == $val['field_id']) {
                    $screen[$key] = $val;
                }
            }
        }

        foreach ($screen as $key => $value) {
            $item = [
                'name'          => $value['label'],
                'field'         => $value['field_id'],
                'type'          => intval($value['screen_type'])
            ];

            if (!is_null($value['screen_default'])) $item['default'] = format_value($value['screen_default']);
            if (!is_null($value['screen_config'])) $item['config'] = $this->formatConfigValue($value['screen_config']);
            if (!is_null($value['screen_config_group'])) $item['configGroup'] = $this->formatConfigValue($value['screen_config_group']);

            if ($item['type'] == 2 || ($item['type'] == 3 && $item['config']['range'])) {
                if (isset($item['default'])) {
                    $item['default'] = format_value($item['default']);
                }
            }

            $screen[$key] = $item;
        }

        if ($data['table_status']) {
            $table = ['column' => [], 'config' => []];

            foreach ($fields as $key => $value) {
                if ($value['is_hiddenable']) continue;

                $item = [
                    'field'     => $value['field_id'],
                    'title'     => $value['label'],
                    'sortable'  => $value['is_sortable']
                ];

                if ($data['default_sort_field'] == $value['field_id']) {
                    $item['sortableDefaultField'] = true;
                    $item['sortableDefaultMode'] = $data['default_sort_mode'] ?: 'asc';
                }

                $config = $this->formatConfigValue(is_null($value['config']) ? '' : $value['config']);

                $table['column'][] = array_merge($item, $config);
            }

            $table['config'] = $this->formatConfigValue(is_null($data['table_config']) ? '' : $data['table_config']);
        } else {
            $table = ['status' => false];
        }

        if ($data['list_status']) {
            $listItem = $data['list_item'] ?: '';
            foreach ($hidden as $key => $value) {
                $listItem = str_replace('{ ' . $value . ' }', '', $listItem);
            }
        } else {
            $listItem = '';
        }

        $data = [
            'list'          => $list,
            'table'         => $table,
            'listItem'      => $listItem,
            'config'        => $this->getListConfig('static')
        ];

        if (isset($list[0]) && isset($list[0]['spec']) && $data['config']['specAll']['status']) {
            $spec = $data['config']['specAll'];

            $item = [ 'field' => 'spec', 'title' => $spec['title'] ];

            if ($spec['sortable']) $item['sortable'] = true;

            $data['table']['column'][] = $item;

            if ($spec['searchable']) $search[] = 'spec';

            if ($spec['screenable']) {
                $item = [
                    'name'      => $spec['title'],
                    'field'     => 'spec',
                    'type'      => intval($spec['screenType'])
                ];

                if ($spec['screenDefault']) $item['default'] = $spec['screenDefault'];
                if ($spec['screenConfig']) $item['config'] = $spec['screenConfig'];
                if ($spec['screenGroupConfig']) $item['configGroup'] = $spec['screenGroupConfig'];

                $screen[] = $item;
            }
        } else {
            $data['listItem'] = str_replace('{ spec }', '', $listItem);
        }

        unset($data['config']['specAll']);

        $data['config']['search']['field'] = $search;

        $data['config']['screen']['list'] = $screen;

        return $data;
    }

    private function dynamicSpec(Spec $spec, Request $request)
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

    private function dynamicSpecs(Spec $spec, Request $request)
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
        $data = array_merge(
            $data[$model],
            ['cuttingSymbol' => $data['cuttingSymbol']],
            ['dataEmptyText' => $data['dataEmptyText']],
            ['sortCaseSensitive' => $data['sortCaseSensitive']]
        );

        if (is_null($data['search']['inputConfig']['componentConfig'])) {
            unset($data['search']['inputConfig']['componentConfig']);
        } else {
            $config = $this->formatConfigValue($data['search']['inputConfig']['componentConfig']);

            unset($data['search']['inputConfig']['componentConfig']);
            $data['search']['inputConfig'] = array_merge($data['search']['inputConfig'], $config2);            
        }

        if (is_null($data['search']['buttonConfig']['componentConfig'])) {
            unset($data['search']['buttonConfig']['componentConfig']);
        } else {
            $config = $this->formatConfigValue($data['search']['buttonConfig']['componentConfig']);

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
            $config = $this->formatConfigValue($data['pagination']['componentConfig']);

            unset($data['pagination']['componentConfig']);
            $data['pagination'] = array_merge($data['pagination'], $config2); 
        }

        $data['pagination']['pageSize'] = is_null($data['pagination']['pageSize']) ? null : intval($data['pagination']['pageSize']);
        $data['pagination']['currentPage'] = is_null($data['pagination']['currentPage']) ? null : intval($data['pagination']['currentPage']);

        if (is_null($data['loading']['config']['componentConfig'])) {
            unset($data['loading']['config']['componentConfig']);
        } else {
            $config = $this->formatConfigValue($data['loading']['config']['componentConfig']);

            unset($data['loading']['config']['componentConfig']);
            $data['loading']['config'] = array_merge($data['loading']['config'], $config2);            
        }

        $data['specAll']['screenDefault'] = format_value($data['specAll']['screenDefault']);
        if (!is_null($data['specAll']['screenConfig'])) {
            $data['specAll']['screenConfig'] = $this->formatConfigValue($data['specAll']['screenConfig']);
        }
        if (!is_null($data['specAll']['screenGroupConfig'])) {
            $data['specAll']['screenGroupConfig'] = $this->formatConfigValue($data['specAll']['screenGroupConfig']);
        }

        return $data;
    }

    private function formatConfigValue(string $data)
    {
        $data = explode('|', $data);

        if (count($data) == 1 && strlen($data[0]) == 0) return [];

        $data2 = [];
        foreach ($data as $key => $value) {
            $value = explode(':', $value);
            $data2[$value[0]] = format_value($value[1]);
        }
        return $data2;
    }
}
