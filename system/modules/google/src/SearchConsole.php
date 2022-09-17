<?php

namespace Google;

/**
 * 
 */
class SearchConsole extends Base
{
    /**
     * 国家/地区列表
     */
    public $countryList = ['ind' => '印度', 'usa' => '美国', 'mys' => '马来西亚', 'esp' => '西班牙', 'rus' => '俄罗斯', 'phl' => '菲律宾', 'sgp' => '新加坡', 'dza' => '阿尔及利亚', 'can' => '加拿大', 'mar' => '摩洛哥', 'gbr' => '英国', 'kor' => '韩国', 'idn' => '印度尼西亚', 'aus' => '澳大利亚', 'fra' => '法国', 'hkg' => '中国香港', 'mex' => '墨西哥', 'pak' => '巴基斯坦', 'sau' => '沙特阿拉伯', 'chl' => '智利', 'ven' => '委内瑞拉', 'bel' => '比利时', 'cze' => '捷克', 'pan' => '巴拿马', 'chn' => '中国', 'syc' => '塞舌尔', 'cmr' => '喀麦隆', 'bra' => '巴西', 'twn' => '台湾', 'arg' => '阿根廷', 'vnm' => '越南', 'ukr' => '乌克兰', 'tur' => '土耳其', 'col' => '哥伦比亚', 'zaf' => '南非', 'are' => '阿拉伯联合酋长国', 'ita' => '意大利', 'jpn' => '日本', 'pol' => '波兰', 'pse' => '巴勒斯坦', 'tha' => '泰国', 'zzz' => '未知地区', 'bgd' => '孟加拉', 'per' => '秘鲁', 'egy' => '埃及', 'deu' => '德国', 'nld' => '荷兰', 'nga' => '尼日利亚', 'swe' => '瑞典', 'bol' => '玻利维亚', 'npl' => '尼泊尔', 'rou' => '罗马尼亚', 'gtm' => '危地马拉', 'nzl' => '新西兰', 'dom' => '多米尼加共和国', 'bgr' => '保加利亚', 'tun' => '突尼斯', 'isr' => '以色列', 'ltu' => '立陶宛', 'khm' => '柬埔寨', 'prt' => '葡萄牙', 'pry' => '巴拉圭', 'che' => '瑞士', 'lbn' => '黎巴嫩', 'ecu' => '厄瓜多尔', 'grc' => '希腊', 'fin' => '芬兰', 'irl' => '爱尔兰', 'cri' => '哥斯达黎加', 'blr' => '白俄罗斯', 'ken' => '肯尼亚', 'kaz' => '哈萨克斯坦', 'qat' => '卡塔尔', 'eth' => '埃塞俄比亚', 'hnd' => '洪都拉斯', 'jor' => '约旦', 'srb' => '塞尔维亚', 'civ' => '科特迪瓦', 'pri' => '波多黎各', 'aut' => '奥地利', 'irn' => '伊朗', 'mmr' => '缅甸', 'cyp' => '塞浦路斯', 'geo' => '格鲁吉亚', 'mda' => '摩尔多瓦', 'lka' => '斯里兰卡', 'brb' => '巴巴多斯', 'ury' => '乌拉圭', 'hrv' => '克罗地亚', 'aze' => '阿塞拜疆', 'jam' => '牙买加', 'hun' => '匈牙利', 'uzb' => '乌兹别克斯坦', 'tto' => '特立尼达和多巴哥', 'nor' => '挪威', 'slv' => '萨尔瓦多', 'mac' => '澳门', 'nam' => '纳米比亚', 'bwa' => '博茨瓦纳', 'omn' => '阿曼', 'alb' => '阿尔巴尼亚', 'isl' => '冰岛', 'mne' => '黑山', 'svk' => '斯洛伐克', 'arm' => '亚美尼亚', 'est' => '爱沙尼亚', 'irq' => '伊拉克', 'bih' => '波斯尼亚和黑塞哥维那', 'hti' => '海地', 'gum' => '关岛', 'mus' => '毛里求斯', 'nic' => '尼加拉瓜', 'kwt' => '科威特', 'sen' => '塞内加尔', 'atg' => '安提瓜和巴布达', 'lux' => '卢森堡', 'mkd' => '马其顿', 'ago' => '安哥拉', 'lva' => '拉脱维亚', 'bhs' => '巴哈马', 'svn' => '斯洛文尼亚', 'dnk' => '丹麦', 'bhr' => '巴林', 'mlt' => '马耳他', 'gab' => '加蓬', 'png' => '巴布亚新几内亚', 'bdi' => '布隆迪', 'glp' => '瓜德罗普', 'btn' => '不丹', 'mwi' => '马拉维', 'gha' => '加纳', 'uga' => '乌干达', 'sdn' => '苏丹', 'grd' => '格林纳达', 'lbr' => '利比里亚', 'tgo' => '多哥', 'rwa' => '卢旺达', 'lso' => '莱索托', 'reu' => '留尼汪岛', 'tza' => '坦桑尼亚', 'lby' => '利比亚', 'mdg' => '马达加斯加', 'ben' => '贝宁', 'yem' => '也门', 'zmb' => '赞比亚'];

    /**
     * 设备列表
     */
    public $deviceList = ['DESKTOP' => '桌面', 'MOBILE' => '移动设备', 'TABLET' => '平板电脑'];

    /**
     * 搜索外观列表
     */
    public $searchAppearanceList = ['PAGE_EXPERIENCE' => '良好页面体验', 'VIDEO' => '视频', 'WEBLITE' => 'Weblight 结果'];

    /**
     * 爬虫设备列表
     */
    public $crawledAsList = ['CRAWLING_USER_AGENT_UNSPECIFIED' => '未知的用户代理', 'DESKTOP' => '桌面用户代理', 'MOBILE' => '移动用户代理'];

    /**
     * robots.txt 状态 是否允许抓取 列表
     */
    public $robotsTxtStateList = ['ROBOTS_TXT_STATE_UNSPECIFIED' => '未知', 'ALLOWED' => '允许', 'DISALLOWED' => '拒绝'];

    /**
     * 是否允许编入索引 状态列表
     */
    public $verdictList = ['VERDICT_UNSPECIFIED' => '未知', 'PASS' => '有效', 'PARTIAL' => '有效但警告', 'FAIL' => '错误或无效', 'NEUTRAL' => '已排除'];

    /**
     * 网页抓取状态列表
     */
    public $pageFetchStateList = ['PAGE_FETCH_STATE_UNSPECIFIED' => '未知的获取状态', 'SUCCESSFUL' => '成功抓取', 'SOFT_404' => '软404', 'BLOCKED_ROBOTS_TXT' => '被 robots.txt 阻止', 'NOT_FOUND' => '未找到 (404)', 'ACCESS_DENIED' => '由于未经授权的请求而被阻止 (401)', 'SERVER_ERROR' => '服务器错误 (5xx)', 'REDIRECT_ERROR' => '重定向错误', 'ACCESS_FORBIDDEN' => '由于访问被禁止 (403) 而被阻止', 'BLOCKED_4XX' => '由于其他 4xx 问题（不是 403、404）而被阻止', 'INTERNAL_CRAWL_ERROR' => '内部错误', 'INVALID_URL' => '无效的网址'];

    function __construct()
    {
        parent::__construct('SearchConsole');
    }

    /**
     * 效果查询
     */
	public function searchAnalytics(array $data): array
	{
        $param = [
            'startDate'     => $data['startDate'],
            'endDate'       => $data['endDate']
        ];

        $groups = [['filters' => []]];

        isset($data['type'])                && $param['type'] = $data['type'];
        isset($data['dimensions'])          && $param['dimensions'] = $data['dimensions'];
        isset($data['country'])             && $filters = ['dimension' => 'country', 'expression' => $data['country']];
        isset($data['device'])              && $filters = ['dimension' => 'device', 'expression' => $data['device']];
        isset($data['page'])                && $filters = ['dimension' => 'page', 'expression' => $data['page']];
        isset($data['query'])               && $filters = ['dimension' => 'query', 'expression' => $data['query']];
        isset($data['searchAppearance'])    && $filters = ['dimension' => 'searchAppearance', 'expression' => $data['searchAppearance']];

        isset($filters)                     && $groups[0]['filters'][] = array_merge($filters, ['operator' => 'contains']);
        $param['dimensionFilterGroups'] = $groups;

        return $this->searchAnalyticsHandleData($this->searchAnalyticsSend($param));
	}

    /**
     * 删除一个站点地图
     * 
     * @param  string $feedpath 站点地图路径
     * @return bool
     */
    public function siteMapDelete(string $feedpath): bool
    {
        $result = $this->class->sitemaps->delete($this->domain, $this->domain . $feedpath);
        $code = $result->getStatusCode();
        return $code >= 200 && $code < 300;
    }

    /**
     * 获取一个站点地图的信息
     * 
     * @param  string $feedpath 站点地图路径
     * @return array
     */
    public function siteMapGet(string $feedpath): array
    {
        $result = $this->class->sitemaps->get($this->domain, $this->domain . $feedpath);

        return [
            'errors'            => $result->errors,
            'isPending'         => $result->isPending,
            'isSitemapsIndex'   => $result->isSitemapsIndex,
            'lastDownloaded'    => $result->lastDownloaded,
            'lastSubmitted'     => $result->lastSubmitted,
            'path'              => $result->path,
            'type'              => $result->type,
            'warnings'          => $result->warnings
        ];
    }

    /**
     * 获取全部站点地图的信息
     * 
     * @return array
     */
    public function siteMapList(): array
    {
        $list = [];
        $result = $this->class->sitemaps->listSitemaps($this->domain);

        $list = json_decode(json_encode($result), true)['sitemap'];

        foreach ($list as $key => $value) {
            $list[$key]['path'] = str_replace($this->domain, '/', $value['path']);
            $list[$key]['type'] = is_null($value['type']) ? '未知' : [
                'atomFeed'          => 'atomFeed',
                'notSitemap'        => '不是站点地图',
                'patternSitemap'    => '模式站点地图',
                'rssFeed'           => 'rssFeed',
                'sitemap'           => '站点地图',
                'urlList'           => 'url列表'
            ][$value['type']];
            $list[$key]['isSitemapsIndex'] = $value['isSitemapsIndex'] ? '是' : '否';
            $list[$key]['isPending'] = $value['isPending'] ? '是' : '否';
            $list[$key]['lastDownloaded'] = is_null($value['lastDownloaded']) ? '' : date('Y-m-d H:i:s', strtotime($value['lastDownloaded']));
            $list[$key]['lastSubmitted'] = is_null($value['lastSubmitted']) ? '' : date('Y-m-d H:i:s', strtotime($value['lastSubmitted']));
        }

        return $list;
    }

    /**
     * 提交一个站点地图
     * 
     * @param  string $feedpath 站点地图路径
     * @return bool
     */
    public function siteMapSubmit(string $feedpath): bool
    {
        $result = $this->class->sitemaps->submit($this->domain, $this->domain . $feedpath);
        $code = $result->getStatusCode();
        return $code >= 200 && $code < 300;
    }

    /**
     * 网址检查获取
     * 
     * @param  string $url      网址
     * @param  string $language 语言
     * @return array
     */
    public function urlInspection(string $url, string $language = null): array
    {
        $body = $this->getInspectUrlIndexRequest();

        $body->setInspectionUrl(Google::domain() . $url);
        $body->setSiteUrl(Google::domain());

        $language && $body->setLanguageCode($language);

        $result = $this->class->urlInspection_index->inspect($body);
        $result = json_decode(json_encode($result), true)['inspectionResult'];

        $result['indexStatusResult']['crawledAs']       && $result['indexStatusResult']['crawledAs']        = $this->crawledAsList[$result['indexStatusResult']['crawledAs']];
        $result['indexStatusResult']['lastCrawlTime']   && $result['indexStatusResult']['lastCrawlTime']    = date('Y-m-d H:i:s', strtotime($result['indexStatusResult']['lastCrawlTime']));
        $result['indexStatusResult']['robotsTxtState']  && $result['indexStatusResult']['robotsTxtState']   = $this->robotsTxtStateList[$result['indexStatusResult']['robotsTxtState']];
        $result['indexStatusResult']['verdict']         && $result['indexStatusResult']['verdict']          = $this->verdictList[$result['indexStatusResult']['verdict']];
        $result['indexStatusResult']['pageFetchState']  && $result['indexStatusResult']['pageFetchState']   = $this->pageFetchStateList[$result['indexStatusResult']['pageFetchState']];

        $result['mobileUsabilityResult']['verdict']     && $result['mobileUsabilityResult']['verdict']      = $this->verdictList[$result['mobileUsabilityResult']['verdict']];

        return $result;
    }

    /**
     * 效果发送请求
     */
    private function searchAnalyticsSend(array $data): array
    {
        $body = $this->getSearchAnalyticsQueryRequest();

        $body->setStartDate($data['startDate']);
        $body->setEndDate($data['endDate']);

        isset($data['type'])                    && $body->setType($data['type']);
        isset($data['rowLimit'])                && $body->setRowLimit($data['rowLimit']);
        isset($data['startRow'])                && $body->setStartRow($data['startRow']);
        isset($data['dataState'])               && $body->setDataState($data['dataState']);
        isset($data['dimensions'])              && $body->setDimensions($data['dimensions']);
        isset($data['searchType'])              && $body->setSearchType($data['searchType']);
        isset($data['aggregationType'])         && $body->setAggregationType($data['aggregationType']);
        isset($data['dimensionFilterGroups'])   && $body->setDimensionFilterGroups($data['dimensionFilterGroups']);

        $result = $this->class->searchanalytics->query($this->domain, $body);

        return json_decode(json_encode($result), true);
    }

    /**
     * 效果处理数据
     */
    private function searchAnalyticsHandleData(array $data): array
    {
        if (isset($data['rows'])) {
            foreach ($data['rows'] as $key => $value) {
                if (isset($value['keys']) && is_array($value['keys']) && isset($value['keys'][0])) {
                    $data['rows'][$key]['keys'] = count($value['keys']) == 1 ? $value['keys'][0] : $value['keys'];
                }
                if (is_string($data['rows'][$key]['keys']) && in_array($data['rows'][$key]['keys'], array_keys($this->countryList))) {
                    $data['rows'][$key]['keys'] = $this->countryList[$data['rows'][$key]['keys']];
                }
                if (is_string($data['rows'][$key]['keys']) && in_array($data['rows'][$key]['keys'], array_keys($this->deviceList))) {
                    $data['rows'][$key]['keys'] = $this->deviceList[$data['rows'][$key]['keys']];
                }
                if (is_string($data['rows'][$key]['keys']) && in_array($data['rows'][$key]['keys'], array_keys($this->searchAppearanceList))) {
                    $data['rows'][$key]['keys'] = $this->searchAppearanceList[$data['rows'][$key]['keys']];
                }
                $data['rows'][$key]['ctr'] = round($value['ctr'] * 100, 1);
                $data['rows'][$key]['position'] = round($value['position'], 1);
            }
        } else {
            foreach ($data as $key => $value) {
                if (isset($value['keys']) && is_array($value['keys']) && isset($value['keys'][0])) {
                    $data[$key]['keys'] = count($value['keys']) == 1 ? $value[$keys][0] : $value['keys'];
                    if (is_string($data[$key]['keys']) && in_array($data[$key]['keys'], array_keys($this->countryList))) {
                        $data[$key]['keys'] = $this->countryList[$data[$key]['keys']];
                    }
                    if (is_string($data[$key]['keys']) && in_array($data[$key]['keys'], array_keys($this->deviceList))) {
                        $data[$key]['keys'] = $this->deviceList[$data[$key]['keys']];
                    }
                    if (is_string($data[$key]['keys']) && in_array($data[$key]['keys'], array_keys($this->searchAppearanceList))) {
                        $data[$key]['keys'] = $this->searchAppearanceList[$data[$key]['keys']];
                    }
                }
                if (isset($value['ctr']) && is_float($value['ctr'])) {
                    $data[$key]['ctr'] = round($value['ctr'] * 100, 1);
                }
                if (isset($value['position']) && is_float($value['position'])) {
                    $data[$key]['position'] = round($value['position'], 1);
                }
            }
        }

        return $data;
    }
}