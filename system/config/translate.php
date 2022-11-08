<?php

return [
	'fields'	=> '[\'url\',\'meta_canonical\',\'image_src\',\'timeout\']',
	'text'		=> '',
	'replace'	=> '',

	'tool'		=> 'azure',
	'mode'		=> 'direct',
	'list'		=> [
		'alibabacloud'	=> [
			'name'			=> '阿里云',
			'mode'			=> 'task'
		],
		'azure'			=> [
			'name'			=> '微软',
			'mode'			=> 'task'
		]
	]
];