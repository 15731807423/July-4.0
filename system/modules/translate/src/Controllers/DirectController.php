<?php

namespace Translate\Controllers;

use Translate\Translate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Translate\Controllers\Concerns\ValidatesTranslationInput;

/**
 * 翻译功能 直接返回结果
 */
class DirectController extends Controller
{
    use ValidatesTranslationInput;

    private $translate;

    function __construct()
    {
        $this->translate = new Translate();
    }

    /**
     * 批量翻译指定页面的全部字段
     */
    public function batch(Request $request)
    {
        // 没有开启多语言
        if (!config('lang.multiple')) return $this->translate->error('没有开启多语言');

        $id = $this->translationNodeIds($request);

        // 没有要翻译的页面
        if (!$id) return $this->translate->error('没有要翻译的页面');

        // 兼容旧调用；新版一键翻译会明确传入每个非翻译源语言。
        $code = $this->translationBatchTarget($request);

        // 没有要翻译的语言
        if (!$code) return $this->translate->error('没有要翻译的语言');

        // 调用并获取结果
        return $this->translate->setTo($code)->setNodes($id)->batch();
    }

    /**
     * 翻译页面
     */
    public function page(Request $request)
    {
        // 没有开启多语言
        if (!config('lang.multiple')) return $this->translate->error('没有开启多语言');

        // 获取数据
        $from = config('lang.translate');
        $requestedCode = $request->input('code');
        $code = $this->translationTarget($request);
        $text = $this->translationText($request);

        if ($from === $requestedCode) return $this->translate->error('不需要翻译');
        if (!$code || !$text) return $this->translate->error('参数有误');

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
        $from = config('lang.translate');
        $requestedCode = $request->input('code');
        $code = $this->translationTarget($request);

        if ($from === $requestedCode) return $this->translate->error('不需要翻译');
        if (!$code) return $this->translate->error('参数有误');

        return $this->translate->setTo($code)->tpl();
    }
}
