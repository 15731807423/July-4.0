<?php

namespace Google\Controllers;

use Google\Google;
use Google\Analytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
* Google Analytics
*/
class AnalyticsController extends Controller
{
    /**
     * 构造函数
     */
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            // 判断是否存在token
            Google::check();

            // 继续加载页面
            return $next($request);
        });
    }

    /**
     * 效果页面展示
     */
    public function a(Request $request, Analytics $model)
    {
        $data = $model->get('2022-08-01', '2022-08-07', ['country'], ['totalUsers']);
        echo json_encode($data);
    }
}
