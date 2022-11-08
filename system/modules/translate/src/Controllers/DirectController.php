<?php

namespace Translate\Controllers;

use Translate\Azure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 直接翻译
 */
class DirectController extends Controller
{
    private $translate;

    function __construct()
    {
        switch (config('translate.tool')) {
            case 'azure':
                $this->translate = new Azure();
                break;
        }
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
        foreach (config('lang.available') as $key => $value) {
            if ($value['translatable'] && $key != $front) $list[] = $key;
        }

        // 没有要翻译的语言
        if (count($list) == 0) return $this->translate->error('没有要翻译的语言');

        // 调用并获取结果
        return $this->translate->setTo($list)->setNodes($id)->batch();
    }

    /**
     * 翻译页面
     */
    public function page(Request $request)
    {
        // 没有开启多语言
        if (!config('lang.multiple')) return $this->translate->error('没有开启多语言');

        // 获取数据
        $from   = config('lang.content');
        $code   = $request->input('code');
        $text   = json_decode($request->input('text'), true);

        if ($from == $code || count($text) == 0) return $this->translate->error('不需要翻译');

        return $this->translate->setTo($code)->page($text);
    }

    /**
     * 翻译模板
     */
    public function tpl(Request $request)
    {
        // 没有开启多语言
        if (!config('lang.multiple')) return $this->translate->error('没有开启多语言');

        // 获取数据
        $from   = config('lang.content');
        $code   = $request->input('code');

        if ($from == $code) return $this->translate->error('不需要翻译');;

        return $this->translate->setTo($code)->tpl();
    }
}
