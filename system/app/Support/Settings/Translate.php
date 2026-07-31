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
        // 页面不再展示翻译平台，但必须保留旧配置，避免保存时丢失该字段。
        'translate.tool' => [
            'key' => 'translate.tool',
            'label' => '翻译平台',
            'description' => '',
        ],

        'translate.mode' => [
            'key' => 'translate.mode',
            'label' => '翻译模式',
            'description' => '任务模式会创建任务并轮询结果，适合批量内容；直接模式会在单次请求中等待并返回结果，内容较多时可能超时。',
        ],

        'translate.code' => [
            'key' => 'translate.code',
            'label' => '代码转换',
            'description' => '网站语言代码与阿里云语言代码不一致时，可在此配置映射关系。',
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
