<?php

require_once "app/start.php";

use \Core\Router;

//Invoca controller Home e executa o método index

// Router::any('/', 'Controllers\\' . DEFAULT_CONTROLLER . '@'. DEFAULT_METHOD);

// Router::any('/(:any)/?(:all)', function($c, $m) {
// 	$path = ($m) ? "Controllers\\{$c}@{$m}" : "Controllers\\{$c}@index";
// 	Router::invokeObject($path);
// });

// Router::run();

Router::autoRun();