<?php

namespace Google;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * 通用方法
 */
class Google
{
	/**
	 * 初始化
	 */
	public static function init()
	{
        $client = new \Google\Client();
        $client->setAuthConfig(config('google'));
        $client->setAccessToken(session('access_token'));
        // $client->setDeveloperKey('AIzaSyCU1BzjecU5Gy6x2mD1RS4Eqqw88iOImT4');
        // $client->setAccessType('offline');
        // $client->setIncludeGrantedScopes(true);

		return $client;
	}

	/**
	 * 获取当前域名
	 */
	public static function domain()
	{
		return rtrim(config('app.url'), '/') . '/';
	}

	/**
	 * 判断是否存在token和站点不存在的处理
	 */
	public static function check()
	{
		// 不存在token 登录
		if (!session('access_token')) return self::login();

        $message = '';

        try {
            if (!self::verification()) {
                $message = '站点不存在，请添加后重试！';
            }
        } catch (\Exception $e) {
            if ($e->getCode() == 401) {
                return self::login();
            } else {
                report($e);
                $message = '站点验证失败，请查看错误日志！';
            }
        }

        if ($message) return view('google::message', ['message' => $message]);

        return true;
	}

	/**
	 * 跳转到谷歌的登录界面 登录前保存当前页面的路由 用来返回页面
	 */
	public static function login()
	{
		session(['google_back' => Route::currentRouteName()]);
		return redirect()->route('manage.google.login')->send();
	}

	/**
	 * 验证站点是否存在 也就是当前账号的 search console 里是否存在当前域名
	 */
	public static function verification()
	{
	    $site = (new SearchConsole())->siteMapList();
	    $url = rtrim(config('app.url'), '/') . '/';

	    foreach ($site as $key => $value) {
	        if ($url === $value['siteUrl']) return true;
	    }

	    return false;
	}
}