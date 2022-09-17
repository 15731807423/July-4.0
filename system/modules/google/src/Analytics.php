<?php

namespace Google;

/**
 * 
 */
class Analytics extends Base
{
    private $property = '';

    function __construct()
    {
        parent::__construct('AnalyticsData');

        $this->property || $this->property = $this->property();
    }

    public function get(string $startDate, string $endDate, array $dimensions, array $metrics): array
    {
        return $this->send([
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'dimensions'        => $dimensions,
            'metrics'           => $metrics
        ]);
    }

    /**
     * 发送请求
     * 
     * @param  array $data 请求参数  startDate为开始时间字符串 endDate为结束时间字符串 dimensions为查询的范围 metrics为查询的指标
     * @return array
     */
	private function send(array $data): array
	{
        $param = [];
        $param['property']      = 'properties/' . $this->property;
        $param['dateRanges'][]  = ['startDate' => $data['startDate'], 'endDate' => $data['endDate']];

        foreach ($data['dimensions'] as $key => $value) {
            $param['dimensions'][] = ['name' => $value];
        }

        foreach ($data['metrics'] as $key => $value) {
            $param['metrics'][] = ['name' => $value];
        }

        $body = $this->getBatchRunReportsRequest();

        $body->setRequests($param);

        return json_decode(json_encode($this->class->properties->batchRunReports($param['property'], $body)), true);
	}

    private function property()
    {
        $class = $this->class('AnalyticsAdmin');

        $data = json_decode(json_encode($class->accountSummaries->listAccountSummaries()), true);

        if (!isset($data['accountSummaries']) || !$data['accountSummaries']) return null;

        $domain = rtrim(ltrim(Google::domain(), 'https://www.'), '/');

        foreach ($data['accountSummaries'] as $key => $value) {
            if ($value['displayName'] != $domain) continue;

            if (!isset($value['propertySummaries']) || !$value['propertySummaries']) return null;

            foreach ($value['propertySummaries'] as $k => $val) {
                if ($val['displayName'] == $domain) return str_replace('properties/', '', $val['property']);
            }
        }
    }
}