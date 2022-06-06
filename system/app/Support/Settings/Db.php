<?php

namespace App\Support\Settings;

class Db extends SettingGroupBase
{
    /**
     * 配置组名称
     *
     * @var string
     */
    protected $name = 'db';

    /**
     * 配置组标题
     *
     * @var string
     */
    protected $title = '数据库设置';

    /**
     * 配置项
     *
     * @var array
     */
    public $items = [];
}
