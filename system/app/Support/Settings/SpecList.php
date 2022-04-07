<?php

namespace App\Support\Settings;

class SpecList extends SettingGroupBase
{
    /**
     * 配置组名称
     *
     * @var string
     */
    protected $name = 'specList';

    /**
     * 配置组标题
     *
     * @var string
     */
    protected $title = '规格列表';

    /**
     * 配置项
     *
     * @var array
     */
    protected $items = [
        'specList.model' => [
            'key' => 'specList.model',
            'label' => '列表模式',
            'description' => '用PHP处理数据或用JS处理数据',
        ],

        'specList.cuttingSymbol' => [
            'key' => 'specList.cuttingSymbol',
            'label' => '切割符号',
            'description' => '数据里多个数据放在一个属性里时的切割符号 空字符串表示不存在多个数据',
        ],

        'specList.dataEmptyText' => [
            'key' => 'specList.dataEmptyText',
            'label' => '数据为空的提示',
            'description' => '页面没有数据时的提示信息',
            'default' => 'No Data'
        ],

        'specList.static.search.status' => [
            'key' => 'specList.static.search.status',
            'label' => '搜索功能',
            'description' => '输入关键词搜索',
        ],

        'specList.static.search.default' => [
            'key' => 'specList.static.search.default',
            'label' => '默认关键词',
            'description' => '页面加载时文本框的默认关键词',
        ],

        'specList.static.search.caseSensitive' => [
            'key' => 'specList.static.search.caseSensitive',
            'label' => '大小写敏感',
            'description' => '搜索时对大小写敏感',
        ],

        'specList.static.search.class' => [
            'key' => 'specList.static.search.class',
            'label' => '搜索功能div的class',
            'description' => '搜索功能div的class',
            'default' => 'data-search'
        ],

        'specList.static.search.inputConfig.onInput' => [
            'key' => 'specList.static.search.inputConfig.onInput',
            'label' => 'input事件触发搜索',
            'description' => '搜索框的input事件触发搜索',
        ],

        'specList.static.search.inputConfig.onChange' => [
            'key' => 'specList.static.search.inputConfig.onChange',
            'label' => 'change事件触发搜索',
            'description' => '搜索框的change事件（回车或失去焦点）触发搜索',
        ],

        'specList.static.search.inputConfig.class' => [
            'key' => 'specList.static.search.inputConfig.class',
            'label' => '搜索框的class',
            'description' => '搜索框的class',
            'default' => 'data-search-input'
        ],

        'specList.static.search.inputConfig.componentConfig' => [
            'key' => 'specList.static.search.inputConfig.componentConfig',
            'label' => '搜索框组件的配置信息',
            'description' => '搜索框组件的配置信息',
        ],

        'specList.static.search.buttonConfig.status' => [
            'key' => 'specList.static.search.buttonConfig.status',
            'label' => '搜索按钮',
            'description' => '搜索框后面显示搜索按钮',
        ],

        'specList.static.search.buttonConfig.text' => [
            'key' => 'specList.static.search.buttonConfig.text',
            'label' => '搜索按钮的文本',
            'description' => '搜索按钮的文本',
            'default' => 'search'
        ],

        'specList.static.search.buttonConfig.class' => [
            'key' => 'specList.static.search.buttonConfig.class',
            'label' => '搜索按钮的class',
            'description' => '搜索按钮的class',
            'default' => 'data-search-button'
        ],

        'specList.static.search.buttonConfig.componentConfig' => [
            'key' => 'specList.static.search.buttonConfig.componentConfig',
            'label' => '搜索按钮组件的配置信息',
            'description' => '搜索按钮组件的配置信息',
        ],

        'specList.static.screen.status' => [
            'key' => 'specList.static.screen.status',
            'label' => '筛选功能',
            'description' => '点击筛选项对数据进行筛选',
        ],

        'specList.static.screen.userStatus' => [
            'key' => 'specList.static.screen.userStatus',
            'label' => '显示已筛选项',
            'description' => '显示用户选择的筛选项',
        ],

        'specList.static.screen.clearText' => [
            'key' => 'specList.static.screen.clearText',
            'label' => '清空已筛选的文本',
            'description' => '清空已筛选的文本',
            'default' => 'reset'
        ],

        'specList.static.screen.selectedClass' => [
            'key' => 'specList.static.screen.selectedClass',
            'label' => '已筛选项div的class',
            'description' => '已筛选项div的class',
            'default' => 'data-screen-selected'
        ],

        'specList.static.screen.countStatus' => [
            'key' => 'specList.static.screen.countStatus',
            'label' => '显示筛选项后面的数值',
            'description' => '显示筛选项后面的数值',
        ],

        'specList.static.screen.groupCountType' => [
            'key' => 'specList.static.screen.groupCountType',
            'label' => '允许计算数值的筛选组类型',
            'description' => '允许计算数值的筛选组类型',
        ],

        'specList.static.screen.type' => [
            'key' => 'specList.static.screen.type',
            'label' => '筛选组之间的关联方式',
            'description' => '筛选组之间的关联方式',
        ],

        'specList.static.screen.nullHidden' => [
            'key' => 'specList.static.screen.nullHidden',
            'label' => '隐藏对应数据数量为0的选项',
            'description' => '隐藏对应数据数量为0的选项',
        ],

        'specList.static.screen.class' => [
            'key' => 'specList.static.screen.class',
            'label' => '筛选功能div的class',
            'description' => '筛选功能div的class',
            'default' => 'data-screen'
        ],

        'specList.static.screen.allClass' => [
            'key' => 'specList.static.screen.allClass',
            'label' => '全部筛选项div的class',
            'description' => '全部筛选项div的class',
            'default' => 'data-screen-all'
        ],

        'specList.static.selector.class' => [
            'key' => 'specList.static.selector.class',
            'label' => '模式选择器div的class',
            'description' => '模式选择器div的class',
            'default' => 'data-selector'
        ],

        'specList.static.selector.list.table.text' => [
            'key' => 'specList.static.selector.list.table.text',
            'label' => '表格按钮文本',
            'description' => '表格按钮文本',
            'default' => 'table'
        ],

        'specList.static.selector.list.list.text' => [
            'key' => 'specList.static.selector.list.list.text',
            'label' => '列表按钮文本',
            'description' => '列表按钮文本',
            'default' => 'list'
        ],

        'specList.static.selector.config.componentConfig' => [
            'key' => 'specList.static.selector.config.componentConfig',
            'label' => '模式选择器组件的配置信息',
            'description' => '模式选择器组件的配置信息',
        ],

        'specList.static.pagination.class' => [
            'key' => 'specList.static.pagination.class',
            'label' => '分页div的class',
            'description' => '分页div的class',
            'default' => 'data-page'
        ],

        'specList.static.pagination.pageSize' => [
            'key' => 'specList.static.pagination.pageSize',
            'label' => '每页数量',
            'description' => '每页展示数据的数量',
            'default' => '10'
        ],

        'specList.static.pagination.currentPage' => [
            'key' => 'specList.static.pagination.currentPage',
            'label' => '默认页',
            'description' => '页面加载时显示的页码',
            'default' => '1'
        ],

        'specList.static.pagination.componentConfig' => [
            'key' => 'specList.static.pagination.componentConfig',
            'label' => '分页组件的配置信息',
            'description' => '分页组件的配置信息',
        ],

        'specList.static.loading.status' => [
            'key' => 'specList.static.loading.status',
            'label' => '加载功能',
            'description' => '加载数据时页面显示加载中',
        ],

        'specList.static.loading.config.componentConfig' => [
            'key' => 'specList.static.loading.config.componentConfig',
            'label' => '加载组件的配置信息',
            'description' => '加载组件的配置信息',
        ],
    ];
}
