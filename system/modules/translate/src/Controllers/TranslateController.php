<?php

namespace Translate\Controllers;

use Translate\Task;
use Translate\Azure;
use Translate\Direct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * 翻译功能
 */
class TranslateController extends Controller
{
    private $tool;

    private $mode;

    private $translate;

    // 微软翻译直接获取结果第三步 控制器的构造函数
    function __construct()
    {
        $this->tool         = config('translate.tool');
        $this->mode         = config('translate.mode');

        if ($this->tool == 'azure') $this->translate = new Azure();
    }

    /**
     * 批量翻译指定页面的全部字段
     * 微软翻译直接获取结果第四步 控制器
     */
    public function batch(Request $request)
    {
        if (!config('lang.multiple')) return $this->translate->error('没有开启多语言');

        $id = $request->input('nodes');
        $id = [1,2];

        if (count($id) == 0) return $this->translate->error('没有要翻译的页面');

        $list   = [];
        $front  = config('lang.frontend');

        foreach (config('lang.available') as $key => $value) {
            if ($value['translatable'] && $key != $front) $list[] = $key;
        }

        if (count($list) == 0) return $this->translate->error('没有要翻译的语言');

        // 微软翻译直接获取结果第五步 调用模型并返回结果
        $result = $this->translate->setTo($list)->setNodes($id)->batch();

        if (count($result) == 0) return $this->translate->error('没有要翻译的内容');

        return $this->translate->batchSuccess($result);
    }

    /**
     * 翻译页面
     */
    public function page(Request $request)
    {
        if (!config('lang.multiple')) return $this->translate->error('没有开启多语言');

        // 获取数据
        $from   = config('lang.content');
        $to     = $request->input('to');
        $text   = json_decode($request->input('text'), true);

        if ($from == $to || count($text) == 0) return $this->translate->pageError('不需要翻译');

        return $this->translate->setTo($to)->page($text);
    }

    /**
     * 翻译模板
     * 
     * @param  string                       $code       语言代码
     */
    public function tpl(Request $request)
    {
        if (!config('lang.multiple')) return $this->translate->error('没有开启多语言');

        $code   = $request->input('code');
        $from   = config('lang.content');

        if ($from == $code) return $this->translate->error('不需要翻译');;

        return $this->translate->setTo($code)->tpl();
    }

    /**
     * 获取翻译结果
     */
    public function result(Request $request)
    {
        $data = $request->all();

        switch ($data['type']) {
            case 'batch':
                return $this->resultBatch($data);
                break;

            case 'page':
                return $this->resultPage($data);
                break;

            case 'tpl':
                return $this->resultTpl($data);
                break;

            default:
                return response('非法操作');
                break;
        }
    }

    /**
     * 获取一键翻译的结果
     * 
     * @param  array  $data 翻译接口返回的数据
     * @return \Illuminate\Http\Response
     */
    private function resultBatch(array $data)
    {
        // 循环获取每个语言的结果
        foreach ($data['lang'] as $key => $value) {
            $data['lang'][$key] = $this->translate->setTo($key)->setNodes($data['nodes'])->resultBatch($value);
        }

        return response($data);
    }

    /**
     * 获取翻译页面的结果
     * 
     * @param  array  $data 翻译接口返回的数据
     * @return \Illuminate\Http\Response
     */
    private function resultPage(array $data)
    {
        $data['result'] = $this->translate->setTo($data['result']['data']['code'])->resultPage($data['result']);

        return response($data);
    }

    /**
     * 根据结果处理模板
     * 
     * @param  array  $data 翻译接口返回的数据
     * @param  string $html 翻译的结果
     * @return \Illuminate\Http\Response
     */
    private function resultTpl(array $data)
    {
        $data['result'] = $this->translate->setTo($data['result']['data']['code'])->resultTpl($data['result']);

        return response($data);
        // 模板路径
        $path = base_path('../themes/frontend/template/');

        // 切割结果
        $html = explode($this->cutting[1], $html);

        // 获取目录下需要翻译的文件路径
        $file = $this->tplFile($path . $data['code']);

        // 按顺序写入文件
        foreach ($file as $key => $value) {
            $handle = fopen($value, 'w');
            fwrite($handle, $html[$key]);
            fclose($handle);
        }

        // 返回修改过的文件的路径
        return response(['file' => $file]);
    }
}
