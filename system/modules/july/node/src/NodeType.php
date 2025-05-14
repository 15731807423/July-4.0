<?php

namespace July\Node;

use App\Entity\EntityMoldBase;
use Illuminate\Support\Facades\Log;

class NodeType extends EntityMoldBase implements GetNodesInterface
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'node_types';

    /**
     * 可批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'label',
        'default_tpl',
        'description',
        'langcode',
        'is_reserved',
    ];

    /**
     * 获取实体类
     *
     * @return string
     */
    public static function getEntityClass()
    {
        return Node::class;
    }

    /**
     * 获取对应的模型集类
     *
     * @return string|null
     */
    public static function getModelSetClass()
    {
        return NodeTypeSet::class;
    }

    public function get_nodes()
    {
        /** @var array */
        $nodeIds = Node::query()->where('mold_id', $this->attributes['id'])->pluck('id')->all();
        return NodeSet::make($nodeIds);
    }


    /**
     * 获取模型模板数据
     *
     * @return array
     */
    public static function template()
    {
        return [
            'id' => null,
            'label' => null,
            'default_tpl' => null,
            'description' => null,
            'langcode' => langcode('content'),
        ];
    }

}
