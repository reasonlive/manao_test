<?php

require_once __DIR__ .'/vendor/autoload.php';
$config = require_once( __DIR__ . '/config.php');

if ($config['env'] === 'development') {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);

	$GLOBALS['css_filetime'] = '?' . filemtime('./src/css/index.css');
	$GLOBALS['js_filetime'] = '?' . filemtime('./src/js/index.js');
}

\Artemiyov\Test\Classes\Router::watch();