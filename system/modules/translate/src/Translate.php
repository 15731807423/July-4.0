<?php
namespace Translate;

use AlibabaCloud\Tea\Tea;
use AlibabaCloud\Tea\Utils\Utils;
use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\Tea\Console\Console;
use AlibabaCloud\SDK\Alimt\V20181012\Alimt;
use AlibabaCloud\SDK\Alimt\V20181012\Models\GetDocTranslateTaskRequest;
use AlibabaCloud\SDK\Alimt\V20181012\Models\CreateDocTranslateTaskRequest;

/**
 * 文档翻译
 */
class Translate
{
	/**
	 * 创建任务
	 * 
	 * @param  string $url 文档url
	 * @return array
	 */
	public static function create(string $url, string $from, string $to)
	{
		$client = self::createClient();

		$request = new CreateDocTranslateTaskRequest([
			'Action'			=> 'CreateDocTranslateTask',
			'sourceLanguage'	=> $from,
			'targetLanguage'	=> $to,
			'fileUrl'			=> $url
		]);

		$result = $client->createDocTranslateTask($request);

		return json_decode(Utils::toJSONString(Tea::merge($result)), true);
	}

	/**
	 * 获取结果
	 * 
	 * @param  string $id 创建文档后返回的taskId
	 * @return array
	 */
	public static function get(string $id)
	{
		$client = self::createClient();

		$request = new GetDocTranslateTaskRequest([
			'Action'			=> 'GetDocTranslateTask',
			'taskId'			=> $id
		]);

		$result = $client->getDocTranslateTask($request);

		return json_decode(Utils::toJSONString(Tea::merge($result)), true);
	}

	private static function createClient()
	{
		$config = new Config([
			'accessKeyId'		=> 'LTAI5tLfbYcHLVCnJQKiofeV',
			'accessKeySecret'	=> 'aNQyyvoJ5IqAen6n3Q299G668Fqr6p'
		]);
		$config->endpoint = 'mt.cn-hangzhou.aliyuncs.com';
		return new Alimt($config);
	}
}