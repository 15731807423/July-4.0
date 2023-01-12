<?php

namespace Specs\Controllers;

use Specs\Spec;
use Specs\Twig;
use Specs\SpecData;
use Specs\Engine;
use Specs\Vue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 前台规格列表页面
 */
class VueController extends Controller
{
    public function list(Request $request)
    {
        $data = $request->all();

        $vue = new Vue();
        return $vue->setSpec($data['specs'] ?? [])->setConfig($data['configThis'] ?? [])->handleData()->list($data);
    }
}
