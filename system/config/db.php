<?php

return [
	'model' => env('DB_CONNECTION'),
	'mysql' => [
		'username' => env('DB_MYSQL_USERNAME'),
		'password' => env('DB_MYSQL_PASSWORD'),
		'database' => env('DB_MYSQL_DATABASE')
	],
	'sqlite' => [
		'database' => env('DB_SQLITE_DATABASE')
	]
];