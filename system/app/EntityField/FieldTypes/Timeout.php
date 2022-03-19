<?php

namespace App\EntityField\FieldTypes;

use Illuminate\Support\Facades\Log;

class Timeout extends FieldTypeBase
{
    /**
     * 类型标志，由小写字符+数字+下划线组成
     *
     * @var string
     */
    protected $handle = 'timeout';

    /**
     * 字段类型标签
     *
     * @var string
     */
    protected $label = '定时器';

    /**
     * 字段类型描述
     *
     * @var string|null
     */
    protected $description = '定时发布页面';

    /**
     * 指定创建或修改字段时可见的参数项
     *
     * @return array
     */
    public function getMetaKeys()
    {
        return [];
    }
}
