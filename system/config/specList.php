<?php

return [
	'model' => 'static',
	'cuttingSymbol' => '',
	'dataEmptyText' => null,
	'sortCaseSensitive' => false,
	'static' => [
		'search' => [
			'status' => false,
			'default' => null,
			'caseSensitive' => null,
			'class' => null,
			'inputConfig' => [
				'onInput' => null,
				'onChange' => null,
				'class' => null,
				'componentConfig' => null
			],
			'buttonConfig' => [
				'status' => false,
				'text' => null,
				'class' => null,
				'componentConfig' => null
			]
		],
		'screen' => [
			'status' => false,
			'userStatus' => false,
			'clearText' => null,
			'selectedClass' => null,
			'countStatus' => false,
			'groupCountType' => [],
			'type' => '1',
			'nullHidden' => false,
			'class' => null,
			'allClass' => null
		],
		'selector' => [
			'class' => null,
			'list' => [
				'table' => [
					'text' => null
				],
				'list' => [
					'text' => null
				]
			],
			'config' => [
				'componentConfig' => null
			]
		],
		'pagination' => [
			'class' => null,
			'pageSize' => null,
			'currentPage' => null,
			'componentConfig' => null
		],
		'loading' => [
			'status' => false,
			'config' => [
				'componentConfig' => null
			]
		],
		'specAll' => [
			'specConfig' => null,
			'status' => false,
			'title' => 'Category',
			'sortable' => false,
			'searchable' => false,
			'screenable' => false,
			'screenType' => '1',
			'screenDefault' => null,
			'screenConfig' => null,
			'screenGroupConfig' => null,
		]
	]
];