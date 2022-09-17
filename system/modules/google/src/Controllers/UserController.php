<?php

namespace Google\Controllers;

use Google\Google;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
* 用户控制器
*/
class UserController extends Controller
{
    /**
     * 登录
     * 如果没有token 跳转到这个页面
     * 登录后也会跳转到这个页面
     * 区别在于 登录后跳转的页面有code参数 登录前没有参数
     */
    public function login(Request $request)
    {
        // 初始化
        $client = new \Google\Client();
        $client->setAuthConfigFile(config('google'));
        // $client->setRedirectUri(Google::domain() . 'manage/google/oauth2callback');
        $client->setRedirectUri('https://www.test.vip/manage/google/login');
        $client->addScope(\Google\Service\SearchConsole::WEBMASTERS);
        $client->addScope(\Google\Service\AnalyticsData::ANALYTICS);
        $client->addScope(\Google\Service\GoogleAnalyticsAdmin::ANALYTICS_READONLY);
        // $client->addScope(\Google\Service\Localservices::ADWORDS);

        if ($code = $request->input('code')) {
            // 如果有code参数 说明这是登录成功的回调页面 用code生成token 保存起来 并重定向回来源页面
            $client->authenticate($code);
            session(['access_token' => $client->getAccessToken()]);
            session()->save();
            return redirect()->route(session('google_back'));
        } else {
            // 否则说明这个页面是没有登录跳转过来的 重定向到谷歌的页面登录认证
            return redirect()->away($client->createAuthUrl());
        }
    }

    /**
     * 退出
     * 退出后需要重新输入账号密码登录认证
     */
    public function logout(Request $request)
    {
        // 如果没有token 直接提示退出成功
        if (!session('access_token')) return view('google::message', ['message' => '退出成功']);

        // 获取实例 移除token 删除session
        $client = Google::init();
        $client->revokeToken(session('access_token'));
        $request->session()->forget('access_token');

        return view('google::message', ['message' => session('access_token') ? '退出失败' : '退出成功']);
    }
}
