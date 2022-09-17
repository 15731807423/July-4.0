<?php

namespace Google\Controllers;

use Google\Google;
use Google\SearchConsole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
* Google Search Console
*/
class SearchConsoleController extends Controller
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

    public function searchAnalytics(Request $request, SearchConsole $model)
    {
        return view('google::SearchConsole.searchanalytics', [
            'url'                       => Google::domain(),
            'query'                     => short_url('manage.google.searchConsole.searchAnalyticsApi'),
            'countryList'               => $model->countryList,
            'deviceList'                => $model->deviceList,
            'searchAppearanceList'      => $model->searchAppearanceList
        ]);
    }

    public function searchAnalyticsApi(Request $request, SearchConsole $model)
    {
        $param = $request->all();

        $data = [
            'startDate'             => $param['startDate'],
            'endDate'               => $param['endDate'],
            'type'                  => $param['type']
        ];

        isset($param['query'])              && $data['query'] = $param['query']; 
        isset($param['page'])               && $data['page'] = Google::domain() . ltrim($param['page'], '/'); 
        isset($param['country'])            && $data['country'] = $param['country']; 
        isset($param['device'])             && $data['device'] = $param['device']; 
        isset($param['searchAppearance'])   && $data['searchAppearance'] = $param['searchAppearance']; 
        isset($param['dimensions'])         && $data['dimensions'] = $param['dimensions'];

        $data = $model->searchAnalytics($data);

        return response(['status' => 1, 'data' => $data]);
    }

    public function siteMap(Request $request, SearchConsole $model)
    {
        return view('google::SearchConsole.sitemaps', [
            'url'           => Google::domain(),
            'submit'        => short_url('manage.google.searchConsole.siteMapSubmitApi'),
            'list'          => short_url('manage.google.searchConsole.siteMapListApi'),
            'delete'        => short_url('manage.google.searchConsole.siteMapDeleteApi')
        ]);
    }

    public function siteMapDeleteApi(Request $request, SearchConsole $model)
    {
        if ($path = $request->input('path')) {
            $result = $model->siteMapDelete($path);
        } else {
            $result = false;
        }

        return response(['status' => intval($result)]);
    }

    public function siteMapListApi(Request $request, SearchConsole $model)
    {
        $list = $model->siteMapList();

        return response(['status' => 1, 'list' => $list]);
    }

    public function siteMapSubmitApi(Request $request, SearchConsole $model)
    {
        if ($path = $request->input('path')) {
            $result = $model->siteMapSubmit($path);
        } else {
            $result = false;
        }

        return response(['status' => intval($result)]);
    }

    public function urlInspection(Request $request, SearchConsole $model)
    {
        return view('google::SearchConsole.inspection', [
            'url'           => Google::domain(),
            'query'         => short_url('manage.google.searchConsole.urlInspectionApi')
        ]);
    }

    public function urlInspectionApi(Request $request, SearchConsole $model)
    {
        if ($url = $request->input('website')) {
            $data = $model->urlInspection($url, $request->input('language', null));
        } else {
            $data = [];
        }

        return response(['status' => 1, 'data' => $data]);
    }

}
