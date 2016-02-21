<?php

/**
 * @file : config.php
 * @author : Fabien Beaujean
 * @description : Configuration of the application
 */

define('FOLDER', 'gcsystem/gcs-micro/');

return [
	'database' => [
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'test',
		'enabled' => false
	],

	'routes' => [
		'index-home' => [
			'url' => '(/*)',
			'action' => 'index.home',
			'vars' => [],
			'method' => 'get',
			'logged' => '*',
			'access' => '*'
		]
	],

	'firewall' => [
		'redirect' => [
			'login' => 'index-home',
			'default' => 'index-home',
		],
		'csrf' => [
			'name' => 'token',
			'enabled' => true
		],
		'error' => 'core/error.php',
		'logged' => 'logged',
		'role' => 'status',
	]
];