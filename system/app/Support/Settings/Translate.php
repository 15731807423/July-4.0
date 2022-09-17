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
        'translate.fields' => [
            'key' => 'translate.fields',
            'label' => '全部不翻译的字段',
            'description' => '',
            'placeholder' => "[\n    'url',\n    'image_src'\n]"
        ],

        'translate.text' => [
            'key' => 'translate.text',
            'label' => '全部不翻译的内容',
            'description' => '',
            'placeholder' => "[\n    'text',\n    'name'\n]"
        ],

        'translate.replace' => [
            'key' => 'translate.replace',
            'label' => '指定翻译结果',
            'description' => '',
            'placeholder' => "[\n    'cn' => [\n        'argger' => '雅格'\n    ]\n]"
        ],
    ];
}
