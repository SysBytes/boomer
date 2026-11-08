<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
//if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
if (file_exists($maintenance = __DIR__ . '/Application/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
//require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Application/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
//$app = require_once __DIR__ . '/../bootstrap/app.php';
$app = require_once __DIR__ . '/Application/bootstrap/app.php';

// Forzamos la ruta pública a la raíz actual
$app->usePublicPath(__DIR__);


$app->handleRequest(Request::capture());
