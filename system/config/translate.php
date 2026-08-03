<?php

return [
	'private_root'	=> env('JULY_TRANSLATE_PRIVATE_ROOT'),
	'public_key_path' => env('JULY_TRANSLATE_PUBLIC_KEY_PATH'),

	'fields'	=> '["url","meta_canonical","image_src","timeout"]',
	'text'		=> '',
	'replace'	=> '',

	'tool'		=> 'alibabacloud',
	'mode'		=> 'task',
	'code'		=> '{"alibabacloud": {"en": "en","cn": "cht"},"tencentcloud": {"en": "en","cn": "zh-TW"},"azure": {"en": "en","cn": "zh-Hant"}}',
	'list'		=> [
		'alibabacloud'	=> [
			'name'			=> '阿里云',
			'mode'			=> 'task'
		],
		'tencentcloud'	=> [
			'name'			=> '腾讯云',
			'mode'			=> 'task'
		],
		'azure'			=> [
			'name'			=> '微软',
			'mode'			=> 'task'
		]
	]
];
