<?php

namespace Specs\Controllers;

use Specs\Spec;
use Specs\Twig;
use Specs\SpecData;
use Specs\Engine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 前台规格列表页面
 */
class DataController extends Controller
{
    private $model;

    /**
     * 构造函数 允许自定义配置信息 定义的配置信息会覆盖后台的配置信息 未定的配置信息不变
     * 
     * @param  array $config 使用的配置信息 会覆盖后台的配置
     */
    function __construct(array $config = [])
    {
        // 初始化一批成员属性
        $this->model = new SpecData();

        // 设置后台的配置信息 用传进来的配置信息覆盖后台的配置信息
        $this->model->setConfig($config);
    }

    /**
     * 设置数据
     * 
     * @param  array 数据列表
     * @return this
     */
    public function setData(array $data)
    {
        $this->model->data = $data;

        return $this;
    }

    /**
     * 设置显示的规格
     * 
     * @param  string|array 字符串表示规格的名字 数组表示多个规格名字的集合
     * @return this
     */
    public function setSpec(string $name)
    {
        $this->model->spec = $name;

        return $this;
    }

    /**
     * 获取规格功能的html
     * 
     * @return string
     */
    public function tplList()
    {
        return $this->model->tpl();
    }
}
