<?php

namespace Specs;

use App\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpecData
{
    /**
     * 全部配置信息 先从后台获取 再用参数覆盖
     */
    public $config;

    private $configUser;

    // 数据
    public $data;

    /**
     * 展示的规格的名字
     */
    public $spec;

    /**
     * 使用配置的规格的模型
     */
    // public $attrSpec;

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
    public function tpl(bool $return = false)
    {
        $this->spec = Spec::where('id', $this->spec)->first();

        // 其他数据
        $data = $this->data();

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
     */
    private function data(): array
    {
        // 使用配置信息的规格的名称
        $specName = $this->spec->getKey();

        // 使用配置信息的规格的字段
        $fields = $this->spec->getFields()->values()->all();

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

        $data = $this->spec->attributesToArray();

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
            'list'          => $this->data,
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
            if (count($value) != 2) continue;
            $data2[$value[0]] = format_value($value[1]);
        }
        return $data2;
    }
}
