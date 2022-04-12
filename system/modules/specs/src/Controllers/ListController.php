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

/**
 * 前台规格列表页面
 */
class ListController extends Controller
{
    private $cuttingSymbol = '';

    /**
     * 规格列表
     *
     * @param  \Specs\Spec                  $spec       规格信息
     * @param  \Illuminate\Http\Request     $request    请求信息
     * @return string
     */
    public function list(Spec $spec, Request $request): string
    {
        $type = config('specList.model', 'static');

        if ($spec->getKey()) {
            return $this->{$type . 'Spec'}($spec, $request);
        } else {
            return $this->staticSpecs($spec, $request);
        }
    }

    /**
     * JS处理单规格列表
     *
     * @param  \Specs\Spec                  $spec       规格信息
     * @param  \Illuminate\Http\Request     $request    请求信息
     * @return string
     */
    private function staticSpec($spec, $request): string
    {
        $data = Engine::make()->specs($spec->getKey())->search()[$spec->getKey()]['records'];

        foreach ($data as $key => $value) $data[$key]['id'] = intval($value['id']);

        $data = $this->staticData($spec, $data);

        if ($keywords = $request->input('keywords')) {
            $data['config']['search']['default'] = $keywords;
        }

        // exit(htmlentities(json_encode($data)));

        // 用js组件 页面路径 themes/frontend/template/specs/list-static.twig 组件路径 front-vue/js/list-static.js
        return html_compress(app('twig')->render('specs/list-static.twig', $data));

        // 直接写在页面里 页面路径 themes/frontend/template/specs/list-static2.twig
        // return html_compress(app('twig')->render('specs/list-static2.twig', $data));
    }

    /**
     * JS处理全部规格列表
     *
     * @param  \Specs\Spec                  $spec       规格信息
     * @param  \Illuminate\Http\Request     $request    请求信息
     * @return string
     */
    private function staticSpecs($spec, $request): string
    {
        $specs = Spec::all()->map(function(Spec $spec) {
            return $spec;
        })->all();

        if ($spec = config('specList.static.specAll.specConfig')) {
            foreach ($specs as $key => $value) {
                if ($spec == $value->attributesToArray()['id']) {
                    $spec = $value;
                }
            }
        } else {
            $spec = $specs[0];
        }

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

        if ($keywords = $request->input('keywords')) {
            $data['config']['search']['default'] = $keywords;
        }

        // exit(htmlentities(json_encode($data)));

        // 用js组件 页面路径 themes/frontend/template/specs/list-static.twig 组件路径 front-vue/js/list-static.js
        return html_compress(app('twig')->render('specs/list-static.twig', $data));

        // 直接写在页面里 页面路径 themes/frontend/template/specs/list-static2.twig
        // return html_compress(app('twig')->render('specs/list-static2.twig', $data));
    }

    /**
     * 获取JS处理时需要用到的配置信息
     *
     * @param  \Specs\Spec      $spec       规格信息
     * @param  array            $list       数据列表
     * @return string
     */
    private function staticData($spec, $list): array
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

            if ($item['type'] == 2 || ($item['type'] == 3 && isset($item['config']['range']) && $item['config']['range'])) {
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
            'config'        => $this->getListConfig()
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

    /**
     * PHP处理单规格列表
     *
     * @param  \Specs\Spec                  $spec       规格信息
     * @param  \Illuminate\Http\Request     $request    请求信息
     * @return string
     */
    private function dynamicSpec($spec, $request): string
    {
        $data = $this->dynamicData($spec);

        if ($keywords = $request->input('keywords')) {
            $data['config']['search']['default'] = $keywords;
        }

        // exit(htmlentities(json_encode($data)));

        // 用js组件 页面路径 themes/frontend/template/specs/list-dynamic.twig 组件路径 front-vue/js/list-dynamic.js
        return html_compress(app('twig')->render('specs/list-dynamic.twig', $data));

        // 直接写在页面里 页面路径 themes/frontend/template/specs/list-dynamic2.twig
        // return html_compress(app('twig')->render('specs/list-dynamic2.twig', $data));
    }

    /**
     * PHP处理全部规格列表
     *
     * @param  \Specs\Spec                  $spec       规格信息
     * @param  \Illuminate\Http\Request     $request    请求信息
     * @return string
     */
    private function dynamicSpecs($spec, $request)
    {
        // 无法从多个数据表里同时查询数据
        // 全部规格列表一律用JS处理
    }

    /**
     * 获取PHP处理时需要用到的配置信息
     *
     * @param  \Specs\Spec      $spec       规格信息
     * @return string
     */
    private function dynamicData($spec): array
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

            if ($item['type'] == 2 || ($item['type'] == 3 && isset($item['config']['range']) && $item['config']['range'])) {
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
            'table'         => $table,
            'listItem'      => $listItem,
            'config'        => $this->getListConfig()
        ];

        unset($data['config']['specAll']);

        $data['config']['search']['field'] = $search;
        $data['config']['screen']['list'] = $screen;

        $data['name'] = $specName;

        return $data;
    }

    /**
     * 获取PHP处理时页面数据的API
     *
     * @param  \Specs\Spec                  $spec       规格信息
     * @param  \Illuminate\Http\Request     $request    请求信息
     * @return string
     */
    public function getlist(Spec $spec, Request $request): array
    {
        // 获取参数
        $search = $request->input('search', null);
        $screen = $request->input('screen', null);
        $sort = $request->input('sort', null);
        $page = $request->input('page', [1, 10]);
        $screenSort = $request->input('screenSort', []);

        // $screen = json_decode($screen, true);
        // $sort = json_decode($sort, true);
        // $page = json_decode($page, true);

        // 配置信息
        $data = $this->dynamicData($spec)['config'];

        // 初始化数据库
        $list = Db::table($spec->getRecordsTable());

        // 如果搜索区分大小写 执行一下sql 最后再恢复回来
        if ($data['search']['caseSensitive']) DB::statement('PRAGMA case_sensitive_like = 1');

        // 根据搜索的参数添加where
        if ($data['search']['status'] && $search) {
            $where = [];
            foreach ($data['search']['field'] as $key => $value) {
                $where[] = '"' . $value . '" like \'%' . $search . '%\'';
            }
            $list->whereRaw('(' . implode(' or ', $where) . ')');
        }

        $query = clone $list;

        // 根据筛选的参数添加where
        if ($data['screen']['status']) {
            foreach ($data['screen']['list'] as $key => $value) {
                $screenValue = [
                    'field'     => $value['field'],
                    'type'      => $value['type'],
                    'value'     => $screen[$value['field']],
                    'config'    => isset($value['config']) ? $value['config'] : []
                ];

                if ($screenValue['type'] == 4 && is_null($screenValue['value'])) $screenValue['value'] = [];

                $list = $this->dynamicAddWhere($screenValue, $list);
            }
        }

        // 筛选的选项
        $screenList = [];

        // 根据字段的值获取全部筛选项
        foreach ($data['screen']['list'] as $key => $value) {
            if (!in_array($value['type'], [1, 2, 5])) continue;

            // 获取全部值去重
            $screenItem = [];
            $list2 = Db::table($spec->getRecordsTable())->pluck($value['field'])->toArray();
            $list2 = array_unique($list2);

            // 根据切割符号切割后放进数组再次去重并排序
            foreach ($list2 as $k => $val) {
                if ($data['cuttingSymbol']) {
                    $screenItem = array_merge($screenItem, explode($data['cuttingSymbol'], $val));
                } else {
                    $screenItem[] = $val;
                }
            }
            $screenItem = array_unique($screenItem);
            sort($screenItem);

            // 单选前面加‘全部’选项并存进数组
            if ($value['type'] == 1 || ($value['type'] == 5 && (!isset($value['config']['multiple']) || !$value['config']['multiple']))) {
                array_unshift($screenItem, 'All');
            }
            $screenList[$value['field']] = $screenItem;
        }

        // 如果需要显示数量或隐藏0 计算每项的数量 使用包含了搜索条件的sql
        if ($data['screen']['countStatus'] || $data['screen']['nullHidden']) {
            if ($data['screen']['type'] == 1 || $data['screen']['type'] == 2) {
                foreach ($screenList as $key => $value) {
                    foreach ($value as $k => $val) {
                        $count = $this->dynamicAddWhere(['field' => $key, 'type' => 1, 'value' => $val], $query)->count();
                        $value[$k] = ['name' => $val, 'count' => $count];
                    }
                    $screenList[$key] = $value;

                    if (is_string($screen[$key])) {
                        $query = $this->dynamicAddWhere(['field' => $key, 'type' => 1, 'value' => $screen[$key]], $query);
                    }

                    if (is_array($screen[$key])) {
                        $query = $this->dynamicAddWhere(['field' => $key, 'type' => 2, 'value' => $screen[$key]], $query);
                    }
                }
            }

            if ($data['screen']['type'] == 3) {
                foreach ($screenSort as $key => $name) {
                    $value = $screenList[$name];
                    foreach ($value as $k => $val) {
                        $count = $this->dynamicAddWhere(['field' => $name, 'type' => 1, 'value' => $val], $query)->count();
                        $value[$k] = ['name' => $val, 'count' => $count];
                    }
                    $screenList[$name] = $value;

                    if (is_string($screen[$name])) {
                        $query = $this->dynamicAddWhere(['field' => $name, 'type' => 1, 'value' => $screen[$name]], $query);
                    }

                    if (is_array($screen[$name])) {
                        $query = $this->dynamicAddWhere(['field' => $name, 'type' => 2, 'value' => $screen[$name]], $query);
                    }
                }

                foreach ($screenList as $key => $value) {
                    if (in_array($key, $screenSort)) continue;

                    foreach ($value as $k => $val) {
                        $count = $this->dynamicAddWhere(['field' => $key, 'type' => 1, 'value' => $val], $query)->count();
                        $value[$k] = ['name' => $val, 'count' => $count];
                    }
                    $screenList[$key] = $value;

                    if (is_string($screen[$key])) {
                        $query = $this->dynamicAddWhere(['field' => $key, 'type' => 1, 'value' => $screen[$key]], $query);
                    }

                    if (is_array($screen[$key])) {
                        $query = $this->dynamicAddWhere(['field' => $key, 'type' => 2, 'value' => $screen[$key]], $query);
                    }
                }
            }

            if ($data['screen']['type'] == 4) {
                foreach ($screenList as $key => $value) {
                    $query2 = clone $query;

                    foreach ($data['screen']['list'] as $k => $val) {
                        if ($key == $val['field']) continue;

                        $screenValue = [
                            'field'     => $val['field'],
                            'type'      => $val['type'],
                            'value'     => $screen[$val['field']],
                            'config'    => isset($val['config']) ? $val['config'] : []
                        ];

                        if ($screenValue['type'] == 4 && is_null($screenValue['value'])) $screenValue['value'] = [];

                        $query2 = $this->dynamicAddWhere($screenValue, $query2);
                    }

                    foreach ($value as $k => $val) {
                        $count = $this->dynamicAddWhere(['field' => $key, 'type' => 1, 'value' => $val], $query2)->count();
                        $value[$k] = ['name' => $val, 'count' => $count];
                    }

                    $screenList[$key] = $value;
                }
            }
        }

        // 排序
        if ($sort) {
            if ($data['sortCaseSensitive']) {
                $list->orderBy($sort['prop'], $sort['order']);
            } else {
                $list->orderBy('lower(' . $sort['prop'] . ')', $sort['order']);
            }
        }

        $count = $list->count();

        $list = $list->offset(($page[0] - 1) * $page[1])->limit($page[1])->get()->toArray();

        // 恢复数据库对大小写敏感的设置
        if ($data['search']['caseSensitive']) DB::statement('PRAGMA case_sensitive_like = 0');

        return ['status' => 1, 'data' => ['screen' => $screenList, 'list' => $list, 'count' => $count]];
    }

    /**
     * 处理搜索信息添加到查询构造器
     * 
     * @param  array                                $data       筛选的数据 ['field' => 'name', 'type' => 1, 'value' => 'bob', 'config' => []]
     * @param  \Illuminate\Database\Query\Builder   $db         查询构造器 Db::table()
     */
    private function dynamicAddWhere(array $data, $db): \Illuminate\Database\Query\Builder
    {
        $db2 = clone $db;

        switch ($data['type']) {
            case 1:
                $data = $this->dynamicHandleWhere($data['field'], $data['value'], 1);
                break;

            case 2:
                $data = $this->dynamicHandleWhere($data['field'], $data['value'], 2);
                break;

            case 3:
                if (isset($data['config']['range']) && $data['config']['range']) {
                    $data = $this->dynamicHandleWhere($data['field'], $data['value'], 3);
                } else {
                    $data = $this->dynamicHandleWhere($data['field'], $data['value'], 1);
                }
                break;

            case 4:
                $data = $this->dynamicHandleWhere($data['field'], $data['value'], 3);
                break;

            case 5:
                if (isset($value['config']['multiple']) && $value['config']['multiple']) {
                    $data = $this->dynamicHandleWhere($data['field'], $data['value'], 2);
                } else {
                    $data = $this->dynamicHandleWhere($data['field'], $data['value'], 1);
                }
                break;
            
            default:
                $data = '';
                break;
        }

        return $data ? $db2->whereRaw($data) : $db2;
    }

    /**
     * 处理搜索条件的值
     * 
     * @param  string               $field      字段名称
     * @param  string|array|int     $value      条件的值
     * @param  int                  $type       筛选类型 1单选等于一个值 2多选再多个值之中 3范围在范围内
     * @return string
     */
    private function dynamicHandleWhere(string $field, $value, int $type): string
    {
        switch ($type) {
            case 1:
                if ($value === 'All') return '';

                return '(' . $this->cuttingSymbolWhere($field, $value) . ')';
                break;

            case 2:
                if (count($value) == 0) return '';

                $where = [];
                foreach ($value as $key => $val) {
                    $where[] = $this->cuttingSymbolWhere($field, $val);
                }
                return '(' . implode(' or ', $where) . ')';
                break;

            case 3:
                if (count($value) != 2) return '';

                if (is_int($value[0]) && is_int($value[1])) {
                    $field .= '+0';
                } else {
                    $value = ['"' . strval($value[0]) . '"', '"' . strval($value[1]) . '"'];
                }

                return '(' . $field . ' BETWEEN ' . $value[0] . ' and ' . $value[1] . ')';
                break;
            
            default:
                return '';
                break;
        }
    }

    /**
     * 处理切割后字段的条件语句
     * 
     * 如果没有切割符号，直接用字段等于值
     * 如果有切割符号，用四个条件筛选
     * @param  string       $field          字段名称
     * @param  string       $value          条件的值
     * @return string
     */
    private function cuttingSymbolWhere(string $field, string $value): string
    {
        return $this->cuttingSymbol ? '("' . $field . '" = \'' . $value . '\' or "' . $field . '" like \'' . $value . $this->cuttingSymbol . '%\' or "' . $field . '" like \'%' . $this->cuttingSymbol . $value . '\' or "' . $field . '" like \'%' . $this->cuttingSymbol . $value . $this->cuttingSymbol . '%\')' : '("' . $field . '" = \'' . $value . '\')';
    }

    /**
     * 获取‘后台-配置-规格列表’的配置信息
     * 
     * @return array
     */
    private function getListConfig(): array
    {
        $data = config('specList', []);

        if (is_null($data['search']['inputConfig']['componentConfig'])) {
            unset($data['search']['inputConfig']['componentConfig']);
        } else {
            $config = $this->formatConfigValue($data['search']['inputConfig']['componentConfig']);

            unset($data['search']['inputConfig']['componentConfig']);
            $data['search']['inputConfig'] = array_merge($data['search']['inputConfig'], $config);            
        }

        if (is_null($data['search']['buttonConfig']['componentConfig'])) {
            unset($data['search']['buttonConfig']['componentConfig']);
        } else {
            $config = $this->formatConfigValue($data['search']['buttonConfig']['componentConfig']);

            unset($data['search']['buttonConfig']['componentConfig']);
            $data['search']['buttonConfig'] = array_merge($data['search']['buttonConfig'], $config);            
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
            $data['pagination'] = array_merge($data['pagination'], $config); 
        }

        $data['pagination']['pageSize'] = is_null($data['pagination']['pageSize']) ? null : intval($data['pagination']['pageSize']);
        $data['pagination']['currentPage'] = is_null($data['pagination']['currentPage']) ? null : intval($data['pagination']['currentPage']);

        if (is_null($data['loading']['config']['componentConfig'])) {
            unset($data['loading']['config']['componentConfig']);
        } else {
            $config = $this->formatConfigValue($data['loading']['config']['componentConfig']);

            unset($data['loading']['config']['componentConfig']);
            $data['loading']['config'] = array_merge($data['loading']['config'], $config);            
        }

        $data['specAll']['screenDefault'] = format_value($data['specAll']['screenDefault']);
        if (!is_null($data['specAll']['screenConfig'])) {
            $data['specAll']['screenConfig'] = $this->formatConfigValue($data['specAll']['screenConfig']);
        }
        if (!is_null($data['specAll']['screenGroupConfig'])) {
            $data['specAll']['screenGroupConfig'] = $this->formatConfigValue($data['specAll']['screenGroupConfig']);
        }

        $this->cuttingSymbol = $data['cuttingSymbol'];

        return $data;
    }

    /**
     * 将配置信息转换成数组
     * 
     * @param  string   $data   后台对组件的配置信息 attr:value|attr2:value2|attr3:value3|...
     * @return array ['attr' => value, 'attr2' => value2, 'attr3' => value3, ...]
     */
    private function formatConfigValue(string $data): array
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
