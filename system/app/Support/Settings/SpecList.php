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
            'description' => '用PHP处理数据或用JS处理数据，仅对单规格的列表生效，全部规格的列表只能用JS处理',
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

        'specList.sortCaseSensitive' => [
            'key' => 'specList.sortCaseSensitive',
            'label' => '排序大小写敏感',
            'description' => '排序时严格区分大小写',
        ],

        'specList.search.status' => [
            'key' => 'specList.search.status',
            'label' => '搜索功能',
            'description' => '输入关键词搜索',
        ],

        'specList.search.default' => [
            'key' => 'specList.search.default',
            'label' => '默认关键词',
            'description' => '页面加载时文本框的默认关键词',
        ],

        'specList.search.caseSensitive' => [
            'key' => 'specList.search.caseSensitive',
            'label' => '大小写敏感',
            'description' => '搜索时对大小写敏感',
        ],

        'specList.search.class' => [
            'key' => 'specList.search.class',
            'label' => '搜索功能div的class',
            'description' => '搜索功能div的class',
            'default' => 'data-search'
        ],

        'specList.search.inputConfig.onInput' => [
            'key' => 'specList.search.inputConfig.onInput',
            'label' => 'input事件触发搜索',
            'description' => '搜索框的input事件触发搜索',
        ],

        'specList.search.inputConfig.onChange' => [
            'key' => 'specList.search.inputConfig.onChange',
            'label' => 'change事件触发搜索',
            'description' => '搜索框的change事件（回车或失去焦点）触发搜索',
        ],

        'specList.search.inputConfig.class' => [
            'key' => 'specList.search.inputConfig.class',
            'label' => '搜索框的class',
            'description' => '搜索框的class',
            'default' => 'data-search-input'
        ],

        'specList.search.inputConfig.componentConfig' => [
            'key' => 'specList.search.inputConfig.componentConfig',
            'label' => '搜索框组件的配置信息',
            'description' => '搜索框组件的配置信息',
        ],

        'specList.search.buttonConfig.status' => [
            'key' => 'specList.search.buttonConfig.status',
            'label' => '搜索按钮',
            'description' => '搜索框后面显示搜索按钮',
        ],

        'specList.search.buttonConfig.text' => [
            'key' => 'specList.search.buttonConfig.text',
            'label' => '搜索按钮的文本',
            'description' => '搜索按钮的文本',
            'default' => 'search'
        ],

        'specList.search.buttonConfig.class' => [
            'key' => 'specList.search.buttonConfig.class',
            'label' => '搜索按钮的class',
            'description' => '搜索按钮的class',
            'default' => 'data-search-button'
        ],

        'specList.search.buttonConfig.componentConfig' => [
            'key' => 'specList.search.buttonConfig.componentConfig',
            'label' => '搜索按钮组件的配置信息',
            'description' => '搜索按钮组件的配置信息',
        ],

        'specList.screen.status' => [
            'key' => 'specList.screen.status',
            'label' => '筛选功能',
            'description' => '点击筛选项对数据进行筛选',
        ],

        'specList.screen.userStatus' => [
            'key' => 'specList.screen.userStatus',
            'label' => '显示已筛选项',
            'description' => '显示用户选择的筛选项',
        ],

        'specList.screen.clearText' => [
            'key' => 'specList.screen.clearText',
            'label' => '清空已筛选的文本',
            'description' => '清空已筛选的文本',
            'default' => 'reset'
        ],

        'specList.screen.selectedClass' => [
            'key' => 'specList.screen.selectedClass',
            'label' => '已筛选项div的class',
            'description' => '已筛选项div的class',
            'default' => 'data-screen-selected'
        ],

        'specList.screen.countStatus' => [
            'key' => 'specList.screen.countStatus',
            'label' => '显示筛选项后面的数值',
            'description' => '显示筛选项后面的数值',
        ],

        'specList.screen.groupCountType' => [
            'key' => 'specList.screen.groupCountType',
            'label' => '允许计算数值的筛选组类型',
            'description' => '允许计算数值的筛选组类型',
        ],

        'specList.screen.type' => [
            'key' => 'specList.screen.type',
            'label' => '筛选组之间的关联方式',
            'description' => '筛选组之间的关联方式',
        ],

        'specList.screen.nullHidden' => [
            'key' => 'specList.screen.nullHidden',
            'label' => '隐藏对应数据数量为0的选项',
            'description' => '隐藏对应数据数量为0的选项',
        ],

        'specList.screen.class' => [
            'key' => 'specList.screen.class',
            'label' => '筛选功能div的class',
            'description' => '筛选功能div的class',
            'default' => 'data-screen'
        ],

        'specList.screen.allClass' => [
            'key' => 'specList.screen.allClass',
            'label' => '全部筛选项div的class',
            'description' => '全部筛选项div的class',
            'default' => 'data-screen-all'
        ],

        'specList.reset.status' => [
            'key' => 'specList.reset.status',
            'label' => '重置搜索和筛选',
            'description' => '重置搜索和筛选'
        ],

        'specList.reset.text' => [
            'key' => 'specList.reset.text',
            'label' => '重置按钮的文本',
            'description' => '重置按钮的文本',
            'default' => 'reset'
        ],

        'specList.reset.class' => [
            'key' => 'specList.reset.class',
            'label' => '重置按钮的class',
            'description' => '重置按钮的class',
            'default' => 'data-reset'
        ],

        'specList.reset.componentConfig' => [
            'key' => 'specList.reset.componentConfig',
            'label' => '重置按钮组件的配置信息',
            'description' => '重置按钮组件的配置信息'
        ],

        'specList.selector.class' => [
            'key' => 'specList.selector.class',
            'label' => '模式选择器div的class',
            'description' => '模式选择器div的class',
            'default' => 'data-selector'
        ],

        'specList.selector.list.table.text' => [
            'key' => 'specList.selector.list.table.text',
            'label' => '表格按钮文本',
            'description' => '表格按钮文本',
            'default' => 'table'
        ],

        'specList.selector.list.table.default' => [
            'key' => 'specList.selector.list.table.default',
            'label' => '表格默认展示',
            'description' => '表格默认展示',
            'default' => true
        ],

        'specList.selector.list.list.text' => [
            'key' => 'specList.selector.list.list.text',
            'label' => '列表按钮文本',
            'description' => '列表按钮文本',
            'default' => 'list'
        ],

        'specList.selector.list.list.default' => [
            'key' => 'specList.selector.list.list.default',
            'label' => '列表默认展示',
            'description' => '列表默认展示',
            'default' => false
        ],

        'specList.selector.componentConfig' => [
            'key' => 'specList.selector.componentConfig',
            'label' => '模式选择器组件的配置信息',
            'description' => '模式选择器组件的配置信息',
        ],

        'specList.pagination.class' => [
            'key' => 'specList.pagination.class',
            'label' => '分页div的class',
            'description' => '分页div的class',
            'default' => 'data-page'
        ],

        'specList.pagination.pageSize' => [
            'key' => 'specList.pagination.pageSize',
            'label' => '每页数量',
            'description' => '每页展示数据的数量',
            'default' => '10'
        ],

        'specList.pagination.currentPage' => [
            'key' => 'specList.pagination.currentPage',
            'label' => '默认页',
            'description' => '页面加载时显示的页码',
            'default' => '1'
        ],

        'specList.pagination.componentConfig' => [
            'key' => 'specList.pagination.componentConfig',
            'label' => '分页组件的配置信息',
            'description' => '分页组件的配置信息',
        ],

        'specList.loading.status' => [
            'key' => 'specList.loading.status',
            'label' => '加载功能',
            'description' => '加载数据时页面显示加载中',
        ],

        'specList.loading.componentConfig' => [
            'key' => 'specList.loading.componentConfig',
            'label' => '加载组件的配置信息',
            'description' => '加载组件的配置信息',
        ],

        'specList.specAll.specConfig' => [
            'key' => 'specList.specAll.specConfig',
            'label' => '查看全部规格时使用的配置信息',
            'description' => '查看全部规格时使用哪个规格的配置信息',
        ],

        'specList.specAll.status' => [
            'key' => 'specList.specAll.status',
            'label' => '查看全部规格时显示‘规格’信息',
            'description' => '查看全部规格时显示‘规格’信息',
        ],

        'specList.specAll.title' => [
            'key' => 'specList.specAll.title',
            'label' => '标题',
            'description' => '‘规格’字段的标题',
            'default' => 'Category'
        ],

        'specList.specAll.order' => [
            'key' => 'specList.specAll.order',
            'label' => '位置',
            'description' => '表格里‘规格’列所在的位置',
            'default' => '1'
        ],

        'specList.specAll.sortable' => [
            'key' => 'specList.specAll.sortable',
            'label' => '可排序',
            'description' => '表格里的‘规格’列允许排序',
        ],

        'specList.specAll.searchable' => [
            'key' => 'specList.specAll.searchable',
            'label' => '可搜索',
            'description' => '列表里的‘规格’列允许搜索，前提是开启了搜索功能',
        ],

        'specList.specAll.screenable' => [
            'key' => 'specList.specAll.screenable',
            'label' => '可筛选',
            'description' => '列表里的‘规格’列允许筛选，前提是开启了筛选功能',
        ],

        'specList.specAll.screenType' => [
            'key' => 'specList.specAll.screenType',
            'label' => '筛选类型',
            'description' => '列表里的‘规格’的筛选类型',
        ],

        'specList.specAll.screenOrder' => [
            'key' => 'specList.specAll.screenOrder',
            'label' => '筛选组位置',
            'description' => '全部筛选组里的位置',
            'default' => '1'
        ],

        'specList.specAll.screenDefault' => [
            'key' => 'specList.specAll.screenDefault',
            'label' => '筛选默认值',
            'description' => '列表里的‘规格’的筛选默认值',
        ],

        'specList.specAll.screenItemOrder' => [
            'key' => 'specList.specAll.screenItemOrder',
            'label' => '筛选项顺序',
            'description' => '用‘|’分割每个项，没有设置的项在后面按顺序排序',
        ],

        'specList.specAll.screenConfig' => [
            'key' => 'specList.specAll.screenConfig',
            'label' => '组件配置',
            'description' => '列表里的‘规格’的筛选组件的配置',
        ],

        'specList.specAll.screenGroupConfig' => [
            'key' => 'specList.specAll.screenGroupConfig',
            'label' => '组件group配置',
            'description' => '列表里的‘规格’的筛选组件group的配置',
        ],
    ];
}
