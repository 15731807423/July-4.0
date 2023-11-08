<?php

namespace App\Support\Settings;

class SiteInformation extends SettingGroupBase
{
    /**
     * 配置组名称
     *
     * @var string
     */
    protected $name = 'site_information';

    /**
     * 配置组标题
     *
     * @var string
     */
    protected $title = '站点信息';

    /**
     * 配置项
     *
     * @var array
     */
    protected $items = [
        'app.url' => [
            'key' => 'app.url',
            'label' => '网址',
            'description' => '首页网址',
        ],

        'site.subject' => [
            'key' => 'site.subject',
            'label' => '主体名称',
            'description' => '网站所服务的主体（企业或个人）的名称',
        ],

        'mail.from.name' => [
            'key' => 'mail.from.name',
            'label' => '发件人名称',
            'description' => '邮件发件人名称',
        ],

        'mail.from.address' => [
            'key' => 'mail.from.address',
            'label' => '发件人地址',
            'description' => '邮件发件人地址',
        ],

        'site.mails' => [
            'key' => 'site.mails',
            'label' => '收件人',
            'description' => '联系表单的默认接收邮箱',
        ],
    ];
}
