<?php

namespace App\Support\Settings;

class Translate extends SettingGroupBase
{
    /**
     * 配置组名称
     *
     * @var string
     */
    protected $name = 'translate';

    /**
     * 配置组标题
     *
     * @var string
     */
    protected $title = '翻译设置';

    /**
     * 配置项
     *
     * @var array
     */
    protected $items = [
        'translate.tool' => [
            'key' => 'translate.tool',
            'label' => '使用的工具',
            'description' => '微软翻译不适用于twig标签和Vue标签，只能用来翻译页面'
        ],

        'translate.mode' => [
            'key' => 'translate.mode',
            'label' => '翻译的模式',
            'description' => '先创建任务再获取任务结果（可能超时）或者直接返回任务结果'
        ],

        'translate.code' => [
            'key' => 'translate.code',
            'label' => '代码转换',
            'description' => '网站上用的代码和翻译平台的代码不一致时配置'
        ],

        'translate.fields' => [
            'key' => 'translate.fields',
            'label' => '全部不翻译的字段',
            'description' => '',
            'placeholder' => "[\"url\",\"image_src\"]"
        ],

        'translate.text' => [
            'key' => 'translate.text',
            'label' => '全部不翻译的内容',
            'description' => '',
            'placeholder' => "[\"text\",\"name\"]"
        ],

        'translate.replace' => [
            'key' => 'translate.replace',
            'label' => '指定翻译结果',
            'description' => '',
            'placeholder' => "[\"cn\":{\"argger\":\"雅格\"}]"
        ],
    ];
}
