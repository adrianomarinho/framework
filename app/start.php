<?php

if (version_compare(PHP_VERSION, '5.3.2', '<')) {
	exit("Sorry, BF1 can only be run on PHP version 5.3.2 or higher! His version: " . PHP_VERSION);
}

define('ROOT_PATH'  		, __DIR__.'/..');
define('VENDOR_PATH'		, __DIR__.'/../vendor');
define('APP_PATH'   		, __DIR__.'/../app');
define('CONTROLLERS_PATH'   , __DIR__.'/../app/Controllers');
define('MODELS_PATH'   		, __DIR__.'/../app/Models');
define('VIEWS_PATH'   		, __DIR__.'/../app/templates');
define('MODULE_PATH'		, __DIR__.'/../app/modules');
define('ASSETS_PATH'		, __DIR__.'/../assets');
define('CSS_PATH'			, __DIR__.'/../assets/css');
define('JS_PATH'			, __DIR__.'/../assets/js');
define('IMG_PATH'			, __DIR__.'/../assets/img');

require VENDOR_PATH.'/autoload.php';

new Core\Config();
