<?php

namespace App\Support\Settings;

class Language extends SettingGroupBase
{
    /**
     * 配置组名称
     *
     * @var string
     */
    protected $name = 'language';

    /**
     * 配置组标题
     *
     * @var string
     */
    protected $title = '语言设置';

    /**
     * 配置项
     *
     * @var array
     */
    protected $items = [
        'lang.multiple' => [
            'key' => 'lang.multiple',
            'label' => '多语言开关',
            'description' => '启用后可对内容进行翻译，可访问多语言页面。',
        ],

        'lang.available' => [
            'key' => 'lang.available',
            'label' => '可用语言',
            'description' => '可用语言及配置。',
        ],

        'lang.content' => [
            'key' => 'lang.content',
            'label' => '内容编辑默认语言',
            'description' => '后台添加内容时的默认语言。',
        ],

        'lang.translate' => [
            'key' => 'lang.translate',
            'label' => '翻译目标默认语言',
            'description' => '后台翻译时的默认源语言。',
        ],

        'lang.icon' => [
            'key' => 'lang.icon',
            'label' => '显示语言图标',
            'description' => '启用后切换语言的按钮前面展示上传的图标。',
        ],

        'lang.frontend' => [
            'key' => 'lang.frontend',
            'label' => '网站默认语言',
            'description' => '网站页面的默认语言。',
        ],
    ];
}
