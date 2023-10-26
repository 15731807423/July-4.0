<?php

namespace Translate\Controllers;

use Translate\Translate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 翻译功能 创建任务后获取任务结果
 */
class TaskController extends Controller
{
    private $translate;

    function __construct()
    {
        $this->translate = new Translate(false);
    }

    /**
     * 批量翻译指定页面的全部字段
     */
    public function batch(Request $request)
    {
        // 没有开启多语言
        if (!config('lang.multiple')) return $this->translate->error('没有开启多语言');

        $id = $request->input('nodes');

        // 没有要翻译的页面
        if (count($id) == 0) return $this->translate->error('没有要翻译的页面');

        $list   = [];
        $front  = config('lang.frontend');

        // 后台配置的语言
        $code = collect(config('lang.available'))->filter(fn ($info, $code) => $code != $front && $info['translatable'])->keys()->get(0);

        // 没有要翻译的语言
        if (!$code) return $this->translate->error('没有要翻译的语言');

        // 调用并获取结果
        return $this->translate->setTo($code)->setNodes($id)->batch();
    }

    /**
     * 获取一键翻译的结果
     */
    public function batchResult(Request $request)
    {
        $data   = $request->input('data');
        $id     = $request->input('nodes');

        if (!json_decode($data, true)) return $this->error('参数有误');

        $front  = config('lang.frontend');

        // 后台配置的语言
        $code = collect(config('lang.available'))->filter(fn ($info, $code) => $code != $front && $info['translatable'])->keys()->get(0);

        return $this->translate->setTo($code)->setNodes($id)->result('batch', $data);
    }

    /**
     * 翻译页面
     */
    public function page(Request $request)
    {
        if (!config('lang.multiple')) return $this->translate->error('没有开启多语言');

        // 获取数据
        $from = config('lang.content');
        $code = $request->input('code');
        $text = json_decode($request->input('text'), true);

        if ($from == $code || count($text) == 0) return $this->translate->error('不需要翻译');

        return $this->translate->setTo($code)->page($text);
    }

    /**
     * 获取翻译页面的结果
     */
    public function pageResult(Request $request)
    {
        $data = $request->input('data');
        $code = $request->input('code');
        $text = json_decode($request->input('text'), true);

        if (!json_decode($data, true)) return $this->error('参数有误');

        return $this->translate->setTo($code)->result('page', $data, $text);
    }

    /**
     * 翻译模板
     */
    public function tpl(Request $request)
    {
        if (!config('lang.multiple')) return $this->translate->error('没有开启多语言');

        $code   = $request->input('code');
        $from   = config('lang.content');

        if ($from == $code) return $this->translate->error('不需要翻译');

        return $this->translate->setTo($code)->tpl();
    }

    /**
     * 根据结果处理模板
     */
    public function tplResult(Request $request)
    {
        $data = $request->input('data');
        $code = $request->input('code');

        if (!json_decode($data, true)) return $this->error('参数有误');

        return $this->translate->setTo($code)->result('tpl', $data);
    }
}
