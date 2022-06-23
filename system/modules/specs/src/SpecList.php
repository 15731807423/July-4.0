<?php

namespace Specs;

use App\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpecList
{
    /**
     * 全部配置信息 先从后台获取 再用参数覆盖
     */
    public $config;

    private $configUser;

    /**
     * 展示的规格的名字 一维数组
     */
    public $specs;

    /**
     * 使用配置的规格的模型
     */
    public $attrSpec;

    /**
     * 自定义的规格的配置
     */
    private $attrData = [];

    /**
     * 允许配置的规格字段的属性
     */
    private $updateSpecFieldAttr = ['label', 'is_groupable', 'screen_type', 'screen_default', 'screen_config', 'screen_config_group', 'is_searchable', 'is_sortable', 'is_hiddenable'];

    /**
     * 允许配置的规格的属性
     */
    private $updateSpecAttr = ['table_status', 'default_sort_field', 'default_sort_mode', 'table_config', 'list_status', 'list_item'];

    /**
     * JS处理规格列表
     *
     * @return string
     */
    public function staticSpec(bool $return = false)
    {
        // 全部数据
        $list = [];

        // 循环获取指定的规格
        foreach ($this->specs as $key => $value) {
            $data = Engine::make()->specs($value)->get()[$value];
            foreach ($data['records'] as $k => $val) {
                $data['records'][$k]['spec'] = $data['attributes']['id'];
                $data['records'][$k]['id'] = intval($val['id']);
            }
            $list = array_merge($list, $data['records']);
        }

        // 其他数据
        $data = $this->staticData($this->attrSpec, $list);

        // if ($keywords = $request->input('keywords')) {
        //     $data['config']['search']['default'] = $keywords;
        // }

        $data['number'] = mt_rand(10000, 99999);

        if ($return) return $data;

        // exit(htmlentities(json_encode($data)));

        // 用js组件 页面路径 themes/frontend/template/specs/list-static.twig 组件路径 themes/backend/js/list-static.js
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
        // 使用配置信息的规格的名称
        $specName = $spec->getKey();

        // 使用配置信息的规格的字段
        $fields = $spec->getFields()->values()->all();

        // 允许修改的规格字段的属性
        $keys = $this->updateSpecFieldAttr;

        // 循环覆盖每个字段
        foreach ($fields as $key => $value) {
            // 自定义信息里没有配置这个字段或者配置信息不是数组或者配置信息是假 则跳过
            if (!array_key_exists($value['field_id'], $this->attrData) || !is_array($this->attrData[$value['field_id']]) || !$this->attrData[$value['field_id']]) continue;

            // 循环自定义信息
            foreach ($this->attrData[$value['field_id']] as $name => $data) {
                // 如果设置的信息属性不合法 则跳过
                if (!in_array($name, $keys)) continue;

                // 覆盖配置信息
                $fields[$key][$name] = $data;
            }
        }

        // 循环每个自定义属性
        foreach ($this->attrData as $name => $data) {
            foreach ($fields as $key => $value) {
                if (in_array($name, $keys) && $value['field_id'] == $name) {
                    $fields[$key] = array_merge($value, $data);
                }
            }
        }

        $data = $spec->attributesToArray();

        $keys = $this->updateSpecAttr;
        foreach ($this->attrData as $key => $value) {
            if (in_array($key, $keys)) {
                $data[$key] = $value;
            }
        }

        $hidden = [];
        foreach ($fields as $key => $value) {
            if ($value['is_hiddenable']) $hidden[] = $value['field_id'];
        }

        $search = $screen = [];
        foreach ($fields as $key => $value) {
            if ($value['is_searchable'] && !$value['is_hiddenable']) $search[] = $value['field_id'];
            if ($value['is_groupable'] && !$value['is_hiddenable']) $screen[] = $value['field_id'];
        }

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
            'config'        => $this->config
        ];

        if (isset($list[0]) && isset($list[0]['spec']) && $data['config']['specAll']['status']) {
            $spec = $data['config']['specAll'];

            $item = ['field' => 'spec', 'title' => $spec['title']];

            if ($spec['sortable']) $item['sortable'] = true;

            $order = intval($spec['order']) ?: 1;

            array_splice($data['table']['column'], $order - 1, 0, [$item]);

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

                $order = intval($spec['screenOrder']) ?: 1;

                array_splice($screen, $order - 1, 0, [$item]);
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
     * PHP处理规格列表
     *
     * @return string
     */
    public function dynamicSpec(bool $return = false)
    {
        $data = $this->dynamicData($this->attrSpec);

        $data['number'] = mt_rand(10000, 99999);

        if ($return) return $data;

        // exit(htmlentities(json_encode($data)));

        // 用js组件 页面路径 themes/frontend/template/specs/list-dynamic.twig 组件路径 themes/backend/js/list-dynamic.js
        return html_compress(app('twig')->render('specs/list-dynamic.twig', $data));

        // 直接写在页面里 页面路径 themes/frontend/template/specs/list-dynamic2.twig
        // return html_compress(app('twig')->render('specs/list-dynamic2.twig', $data));
    }

    /**
     * 获取PHP处理时需要用到的配置信息
     *
     * @param  \Specs\Spec      $spec       规格信息
     * @return string
     */
    private function dynamicData($spec): array
    {
        // 使用配置信息的规格的名称
        $specName = $spec->getKey();

        // 使用配置信息的规格的字段
        $fields = $spec->getFields()->values()->all();

        // 允许修改的规格字段的属性
        $keys = $this->updateSpecFieldAttr;

        // 循环覆盖每个字段
        foreach ($fields as $key => $value) {
            // 自定义信息里没有配置这个字段或者配置信息不是数组或者配置信息是假 则跳过
            if (!array_key_exists($value['field_id'], $this->attrData) || !is_array($this->attrData[$value['field_id']]) || !$this->attrData[$value['field_id']]) continue;

            // 循环自定义信息
            foreach ($this->attrData[$value['field_id']] as $name => $data) {
                // 如果设置的信息属性不合法 则跳过
                if (!in_array($name, $keys)) continue;

                // 覆盖配置信息
                $fields[$key][$name] = $data;
            }
        }

        // 循环每个自定义属性
        foreach ($this->attrData as $name => $data) {
            foreach ($fields as $key => $value) {
                if (in_array($name, $keys) && $value['field_id'] == $name) {
                    $fields[$key] = array_merge($value, $data);
                }
            }
        }

        $data = $spec->attributesToArray();

        $keys = $this->updateSpecAttr;
        foreach ($this->attrData as $key => $value) {
            if (in_array($key, $keys)) {
                $data[$key] = $value;
            }
        }

        $hidden = [];
        foreach ($fields as $key => $value) {
            if ($value['is_hiddenable']) $hidden[] = $value['field_id'];
        }

        $search = $screen = [];
        foreach ($fields as $key => $value) {
            if ($value['is_searchable'] && !$value['is_hiddenable']) $search[] = $value['field_id'];
            if ($value['is_groupable'] && !$value['is_hiddenable']) $screen[] = $value['field_id'];
        }

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
            'config'        => $this->config
        ];

        if ($data['config']['specAll']['status']) {
            $spec = $data['config']['specAll'];

            $item = ['field' => 'spec', 'title' => $spec['title']];

            if ($spec['sortable']) $item['sortable'] = true;

            $order = intval($spec['order']) ?: 1;

            array_splice($data['table']['column'], $order - 1, 0, [$item]);

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

                $order = intval($spec['screenOrder']) ?: 1;

                array_splice($screen, $order - 1, 0, [$item]);
            }
        } else {
            $data['listItem'] = str_replace('{ spec }', '', $listItem);
        }

        unset($data['config']['specAll']);

        $data['config']['search']['field'] = $search;
        $data['config']['screen']['list'] = $screen;

        $data['name'] = $this->specs;
        $data['configUser'] = json_encode($this->configUser);

        return $data;
    }

    private function getTableData(Spec $spec, array $data, Request $request)
    {
        // 获取参数
        $search = $request->input('search', null);
        $screen = $request->input('screen', null);
        $screenSort = $request->input('screenSort', []);
        $table = $spec->getRecordsTable();

        // 初始化数据库
        $list = Db::table($table)->select(DB::raw('*, "' . $spec->getKey() . '" as spec'));

        // 如果搜索区分大小写 执行一下sql 最后再恢复回来
        // if ($data['search']['caseSensitive']) DB::statement('PRAGMA case_sensitive_like = 1');

        // 根据搜索的参数添加where
        if ($data['search']['status'] && $search) {
            $where = [];
            foreach ($data['search']['field'] as $key => $value) {
                if ($value == 'spec') {
                    $where[] = '"' . $spec->getKey() . '" like \'%' . $search . '%\'';
                } else {
                    $where[] = '`' . $value . '` like \'%' . $search . '%\'';
                }
            }
            $list->whereRaw('(' . implode(' or ', $where) . ')');
        }

        $query = clone $list;

        // 根据筛选的参数添加where
        if ($data['screen']['status']) {
            foreach ($data['screen']['list'] as $key => $value) {
                if ($value['field'] == 'spec') {
                    if ($screen['spec'] == 'All') continue;
                    $list->whereRaw('"' . $spec->getKey() . '" = "' . $screen[$value['field']] . '"');
                    continue;
                }
                $screenValue = [
                    'field'     => $value['field'],
                    'type'      => $value['type'],
                    'value'     => $screen[$value['field']],
                    'config'    => isset($value['config']) ? $value['config'] : []
                ];

                $list = $this->dynamicAddWhere($screenValue, $list);
            }
        }

        // $list->dd();

        // 筛选的选项
        $screenList = [];

        // 根据字段的值获取全部筛选项
        foreach ($data['screen']['list'] as $key => $value) {
            if (!in_array($value['type'], [1, 2, 5])) continue;

            // 获取字段出现过的全部值
            $screenItem = [];

            if ($value['field'] == 'spec') {
                $list2 = $this->specs;
            } else {
                $list2 = Db::table($table)->groupBy($value['field'])->pluck($value['field'])->toArray();
            }

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

            // 单选前面加‘全部’选项并存进数组 单选 或 下拉菜单单选没有清除按钮
            if ($value['type'] == 1 || ($value['type'] == 5 && (!isset($value['config']['multiple']) || !$value['config']['multiple']) && (!isset($value['config']['clearable']) || !$value['config']['clearable']))) {
                array_unshift($screenItem, 'All');
            }
            $screenList[$value['field']] = $screenItem;
        }

        // 如果需要显示数量或隐藏0 计算每项的数量 使用包含了搜索条件的sql 否则不计算数值 全部设置为0
        if ($data['screen']['countStatus'] || $data['screen']['nullHidden']) {
            if ($data['screen']['type'] == 1 || $data['screen']['type'] == 2) {
                foreach ($screenList as $key => $value) {
                    foreach ($value as $k => $val) {
                        if ($key == 'spec') {
                            $count = $spec->getKey() == $val || 'All' == $val ? $query->count() : 0;
                        } else {
                            $count = $this->dynamicAddWhere(['field' => $key, 'type' => 1, 'value' => $val], $query)->count();
                        }

                        $value[$k] = ['name' => $val, 'count' => $count];
                    }
                    $screenList[$key] = $value;

                    if (is_string($screen[$key])) {
                        if ($key == 'spec') {
                            if ($screen[$key] != 'All') $query->whereRaw('"' . $spec->getKey() . '" = "' . $screen[$key] . '"');
                        } else {
                            $query = $this->dynamicAddWhere(['field' => $key, 'type' => 1, 'value' => $screen[$key]], $query);
                        }
                    }

                    if (is_array($screen[$key])) {
                        if ($key == 'spec') {
                            if ($screen[$key] != 'All') $query->whereRaw('"' . $spec->getKey() . '" in "' . implode(',', $screen[$key]) . '"');
                        } else {
                            $query = $this->dynamicAddWhere(['field' => $key, 'type' => 2, 'value' => $screen[$key]], $query);
                        }
                    }
                }
            }

            if ($data['screen']['type'] == 3) {
                foreach ($screenSort as $key => $name) {
                    $value = $screenList[$name];
                    foreach ($value as $k => $val) {
                        if ($name == 'spec') {
                            $count = $spec->getKey() == $val || 'All' == $val ? $query->count() : 0;
                        } else {
                            $count = $this->dynamicAddWhere(['field' => $name, 'type' => 1, 'value' => $val], $query)->count();
                        }

                        $value[$k] = ['name' => $val, 'count' => $count];
                    }
                    $screenList[$name] = $value;

                    if (is_string($screen[$name])) {
                        if ($name == 'spec') {
                            if ($screen[$name] != 'All') $query->whereRaw('"' . $spec->getKey() . '" = "' . $screen[$name] . '"');
                        } else {
                            $query = $this->dynamicAddWhere(['field' => $name, 'type' => 1, 'value' => $screen[$name]], $query);
                        }
                    }

                    if (is_array($screen[$name])) {
                        if ($name == 'spec') {
                            if ($screen[$name] != 'All') $query->whereRaw('"' . $spec->getKey() . '" in "' . implode(',', $screen[$name]) . '"');
                        } else {
                            $query = $this->dynamicAddWhere(['field' => $name, 'type' => 2, 'value' => $screen[$name]], $query);
                        }
                    }
                }

                foreach ($screenList as $key => $value) {
                    if (in_array($key, $screenSort)) continue;

                    foreach ($value as $k => $val) {
                        if ($key == 'spec') {
                            $count = $spec->getKey() == $val || 'All' == $val ? $query->count() : 0;
                        } else {
                            $count = $this->dynamicAddWhere(['field' => $key, 'type' => 1, 'value' => $val], $query)->count();
                        }
                        $value[$k] = ['name' => $val, 'count' => $count];
                    }
                    $screenList[$key] = $value;

                    if (is_string($screen[$key])) {
                        if ($key == 'spec') {
                            if ($screen[$key] != 'All') $query->whereRaw('"' . $spec->getKey() . '" = "' . $screen[$key] . '"');
                        } else {
                            $query = $this->dynamicAddWhere(['field' => $key, 'type' => 1, 'value' => $screen[$key]], $query);
                        }
                    }

                    if (is_array($screen[$key])) {
                        if ($key == 'spec') {
                            if ($screen[$key] != 'All') $query->whereRaw('"' . $spec->getKey() . '" = "' . $screen[$key] . '"');
                        } else {
                            $query = $this->dynamicAddWhere(['field' => $key, 'type' => 2, 'value' => $screen[$key]], $query);
                        }
                    }
                }
            }

            if ($data['screen']['type'] == 4) {
                foreach ($screenList as $key => $value) {
                    $query2 = clone $query;

                    foreach ($data['screen']['list'] as $k => $val) {
                        if ($key == $val['field']) continue;

                        if ($val['field'] == 'spec') {
                            if ($screen['spec'] == 'All') continue;
                            $query2->whereRaw('"' . $spec->getKey() . '" = "' . $screen[$val['field']] . '"');
                            continue;
                        }

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
                        if ($key == 'spec') {
                            $count = $spec->getKey() == $val || 'All' == $val ? $query2->count() : 0;
                        } else {
                            $count = $this->dynamicAddWhere(['field' => $key, 'type' => 1, 'value' => $val], $query2)->count();
                        }
                        $value[$k] = ['name' => $val, 'count' => $count];
                    }

                    $screenList[$key] = $value;
                }
            }
        } else {
            foreach ($screenList as $key => $value) {
                foreach ($value as $k => $val) {
                    $value[$k] = ['name' => $val, 'count' => 0];
                }
                $screenList[$key] = $value;
            }
        }

        // 恢复数据库对大小写敏感的设置
        // if ($data['search']['caseSensitive']) DB::statement('PRAGMA case_sensitive_like = 0');

        return [$list, $screenList];
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
                if (!is_array($data['value']) || count($data['value']) != 2) return $db;
                $data['value'] = [date('Y-m-d H:i:s', $data['value'][0]), date('Y-m-d H:i:s', $data['value'][1])];
                $data = $this->dynamicHandleWhere($data['field'], $data['value'], 3);
                break;

            case 5:
                if (isset($data['config']['multiple']) && $data['config']['multiple']) {
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
                if (!is_string($value) || strlen($value) == 0 || $value === 'All') return '';

                return $this->cuttingSymbolWhere($field, $value);
                break;

            case 2:
                if (count($value) == 0) return '';

                $where = [];
                foreach ($value as $key => $val) {
                    $where[] = $this->cuttingSymbolWhere($field, strval($val));
                }
                return '(' . implode(' or ', $where) . ')';
                break;

            case 3:
                if (!is_array($value) || count($value) != 2) return '';

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
        return $this->config['cuttingSymbol'] ? '(`' . $field . '` = \'' . $value . '\' or `' . $field . '` like \'' . $value . $this->config['cuttingSymbol'] . '%\' or `' . $field . '` like \'%' . $this->config['cuttingSymbol'] . $value . '\' or `' . $field . '` like \'%' . $this->config['cuttingSymbol'] . $value . $this->config['cuttingSymbol'] . '%\')' : '(`' . $field . '` = \'' . $value . '\')';
    }

    /**
     * 设置‘后台-配置-规格列表’的配置信息
     * 
     * @param  array $config 自定义配置信息
     */
    public function setConfig(array $config)
    {
        // 获取后台的配置信息
        $data = config('specList', []);

        $this->configUser = $config;

        // 覆盖
        foreach ($config as $key => $value) {
            if (strpos($key, 'specList.') !== 0) continue;

            $key = explode('.', str_replace('specList.', '', $key));
            $last = array_splice($key, count($key) - 1, 1)[0];
            foreach ($key as $k => $val) $key[$k] = '[\'' . $val . '\']';
            $key = implode('', $key);

            $attr = '$data' . $key;

            if (is_int($value)) {
                $value = strval($value);
                $string = false;
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
                $string = false;
            } elseif (is_null($value)) {
                $value = 'null';
                $string = false;
            } else {
                $string = true;
            }

            if (eval('return array_key_exists("' . $last . '", ' . $attr . ');')) {
                eval($attr . '["' . $last . '"]' . ' = ' . ($string ? '"' . $value . '"' : $value) . ';');
            }
        }

        if (isset($config['spec'])) {
            $this->attrData = $config['spec'];
        }

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

        if (is_null($data['selector']['config']['componentConfig'])) {
            unset($data['selector']['config']['componentConfig']);
        } else {
            $config = $this->formatConfigValue($data['selector']['config']['componentConfig']);

            unset($data['selector']['config']['componentConfig']);
            $data['selector']['config'] = array_merge($data['selector']['config'], $config);            
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

        $this->config = $data;
    }

    /**
     * 将配置信息转换成数组
     * 
     * @param  string   $data   后台对组件的配置信息 attr:value|attr2:value2|attr3:value3|...
     * @return array ['attr' => value, 'attr2' => value2, 'attr3' => value3, ...]
     */
    private function formatConfigValue(string $data): array
    {
        if (strlen($data) == 0) return [];

        $data = explode('|', $data);

        $data2 = [];
        foreach ($data as $key => $value) {
            $value = explode(':', $value, 2);
            $data2[$value[0]] = format_value($value[1]);
        }
        return $data2;
    }

    private function screenMerge(...$arrays)
    {
        $list = array_splice($arrays, 0, 1)[0];

        foreach ($arrays as $array) {
            foreach ($array as $data) {
                $status = false;
                foreach ($list as $key => $old) {
                    if ($data['name'] == $old['name']) {
                        $status = $key;
                        break;
                    }
                }

                if ($status === false) {
                    $list[] = $data;
                } else {
                    $list[$key]['count'] += $data['count'];
                }
            }
        }

        return $list;
    }
}
