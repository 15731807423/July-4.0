<?php

return [
	'fields'	=> '[\'url\',\'meta_canonical\',\'image_src\',\'timeout\']',
	'text'		=> '',
	'replace'	=> '',

	'tool'		=> 'alibabacloud',
	'mode'		=> 'task',
	'code'		=> '[
	\'alibabacloud\' => [
		\'en\' => \'en\',
	],
	\'tencentcloud\' => [
		\'en\' => \'en\',
	],
	\'azure\' => [
		\'en\' => \'en\',
	],
]',
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