<?php

namespace Installer\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Installer\Update;

class UpdateController extends Controller
{
    /**
     * 安装步骤 0：显示安装界面
     *
     * @return \Illuminate\View\View
     */
    public function dbHome()
    {
        return view('installer::db-update', ['data' => config('db')]);
    }

    /**
     * 安装步骤 1：更新 .env 文件
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function dbUpdate(Request $request)
    {
        return Update::dbUpdate($request->all());
    }
}
