<?php

namespace Google;

use Google\Service\AnalyticsData;
use Google\Service\SearchConsole;
use Google\Service\GoogleAnalyticsAdmin;
use Google\Service\SearchConsole\InspectUrlIndexRequest;
use Google\Service\SearchConsole\SearchAnalyticsQueryRequest;
use Google\Service\AnalyticsData\BatchRunReportsRequest;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V11\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V11\GoogleAdsClientBuilder;

/**
 * 
 */
class Base
{
    /**
     * 核心类 所有操作都在里面
     */
    protected $class;

    /**
     * 当前域名 所有请求在传入域名时都用的这个域名 前端无法修改 但可以在后端修改
     */
    protected $domain;

    private $inspectUrlIndexRequest;

    private $searchAnalyticsQueryRequest;

    private $batchRunReportsRequest;

    /**
     * 构造函数 初始化一批成员属性
     */
    function __construct($class)
    {
        // 如果没有token 去登录
        if (!session('access_token')) return redirect()->route('manage.google.login');

        $this->class = $this->class($class);

        $this->domain = Google::domain();
    }

    protected function class(string $name)
    {
        switch ($name) {
            case 'SearchConsole':
                return new SearchConsole(Google::init());
                break;

            case 'AnalyticsData':
                return new AnalyticsData(Google::init());
                break;

            case 'AnalyticsAdmin':
                return new GoogleAnalyticsAdmin(Google::init());
                break;

            case 'Ads':
                $oAuth2Credential = (new OAuth2TokenBuilder())
                ->withClientId(config('google.web.client_id'))
                ->withClientSecret(config('google.web.client_secret'))
                ->withRefreshToken(config('google.web.refresh_token'))
                ->build();

                $googleAdsClient = (new GoogleAdsClientBuilder())
                ->withDeveloperToken(config('google.web.developer_token'))
                ->withLoginCustomerId(config('google.web.login_customer_id'))
                ->withOAuth2Credential($oAuth2Credential)
                ->build();

                return $googleAdsClient->getGoogleAdsServiceClient();
                break;
        }
    }

    /**
     * 获取相关参数实例
     */
    protected function getInspectUrlIndexRequest(): InspectUrlIndexRequest
    {
    	return $this->inspectUrlIndexRequest ?: $this->inspectUrlIndexRequest = new InspectUrlIndexRequest();
    }

    /**
     * 获取相关参数实例
     */
	protected function getSearchAnalyticsQueryRequest(): SearchAnalyticsQueryRequest
	{
		return $this->searchAnalyticsQueryRequest ?: $this->searchAnalyticsQueryRequest = new SearchAnalyticsQueryRequest();
	}

    /**
     * 获取相关参数实例
     */
    protected function getBatchRunReportsRequest(): BatchRunReportsRequest
    {
        return $this->batchRunReportsRequest ?: $this->batchRunReportsRequest = new BatchRunReportsRequest();
    }
}