<?php

use App\Slim\App;
use Ronanchilvers\Container\Slim\Container;

if (PHP_SAPI == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require("../vendor/autoload.php");

$container = new Container([
    'settings' => include('../config/settings.php')
]);

// Load app services
include("../config/services.php");

// Create the App object
$app = new App($container);
include("../config/middleware.php");
include("../config/routes.php");
$app->run();
