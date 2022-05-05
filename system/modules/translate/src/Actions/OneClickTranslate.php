<?php

namespace Translate\Actions;

use Illuminate\Http\Request;
use App\Http\Actions\ActionBase;
use Translate\Controllers\TranslateController;

/**
 * 一键翻译
 *
 * @return \Illuminate\Http\Response
 */
class OneClickTranslate extends ActionBase
{
    protected static $routeName = 'one-click-translate';

    protected static $title = '一键翻译';

    public function __invoke(Request $request)
    {
        return (new TranslateController())->all();
    }
}
