<?php

namespace Specs;

use App\Casts\Serialized;
use App\Models\ModelBase;

class SpecField extends ModelBase
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'spec_fields';

    /**
     * 可批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'field_id',
        'spec_id',
        'label',
        'description',
        'field_type_id',
        'default',
        'options',
        'places',
        'is_unique',
        'is_groupable',
        'is_searchable',
        'is_sortable',
        'is_hiddenable',
        'config',
        'is_deleted',
        'delta',
        'screen_type',
        'screen_default',
        'screen_config',
        'screen_config_group'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'default' => Serialized::class,
        'options' => Serialized::class,
        'is_unique' => 'bool',
        'is_groupable' => 'bool',
        'is_searchable' => 'bool',
        'is_sortable' => 'bool',
        'is_hiddenable' => 'bool',
        'is_deleted' => 'bool',
    ];

    /**
     * 属性及默认值
     *
     * @return array
     */
    public static function defaultAttributes()
    {
        return [
            'field_id' => null,
            'spec_id' => null,
            'label' => null,
            'description' => null,
            'field_type_id' => 'text',
            'default' => null,
            'options' => [],
            'places' => null,
            'is_unique' => false,
            'is_groupable' => false,
            'is_searchable' => true,
            'is_sortable' => false,
            'is_hiddenable' => false,
            'config' => null,
            'is_deleted' => false,
            'delta' => 0,
            'screen_type' => 1,
            'screen_default' => null,
            'screen_config' => null,
            'screen_config_group' => null
        ];
    }

    /**
     * @return \Specs\FieldTypeDefinitions\DefinitionInterface
     */
    public function getFieldType()
    {
        return FieldType::findOrFail($this->attributes['field_type_id'])->bind($this->attributesToArray());
    }
}
