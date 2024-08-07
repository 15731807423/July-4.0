<?php

return [
    // 多语言开关
    'multiple' => false,

    // 后端资源默认语言
    'backend' => 'zh',

    // 前端资源默认语言
    'frontend' => 'en',

    // 内容编辑默认语言
    'content' => 'en',

    // 翻译目标默认语言
    'translate' => 'en',

    // 可用语言（代码）列表
    'available' => [
        'en' => [
            'accessible' => true,   // 前端可访问该语言页面
            'translatable' => true, // 内容可翻译为该语言
            'name' => '英语',
            'code' => 'en'
        ]
    ],

    // 可选语言（代码）列表
    'all' => [
        'ar' => [
            'native' => 'العربية',
            'dir' => 'rtl',
        ],
        'de' => [
            'native' => 'Deutsch',
            'dir' => 'ltr',
        ],
        'en' => [
            'native' => 'English',
            'dir' => 'ltr',
        ],
        'es' => [
            'native' => 'Español',
            'dir' => 'ltr',
        ],
        'fr' => [
            'native' => 'français',
            'dir' => 'ltr',
        ],
        'fil' => [
            'native' => 'Pilipino',
            'dir' => 'ltr',
        ],
        'hi' => [
            'native' => 'हिन्दी, हिंदी',
            'dir' => 'ltr',
        ],
        'it' => [
            'native' => 'Italiano',
            'dir' => 'ltr',
        ],
        'ja' => [
            'native' => '日本語',
            'dir' => 'ltr',
        ],
        'pt-PT' => [
            'native' => 'Português',
            'dir' => 'ltr',
        ],
        'ru' => [
            'native' => 'русский',
            'dir' => 'ltr',
        ],
        'zh' => [
            'native' => '简体中文',
            'dir' => 'ltr',
        ],
        'zh-tw' => [
            'native' => '繁体中文',
            'dir' => 'ltr'
        ],
    ],
];
