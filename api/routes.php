<?php

use App\Controllers\AuthController;
use Slim\Routing\RouteCollectorProxy;

if(!isset($app)) {
    exit();
}

$app->group('/API/v1', function(RouteCollectorProxy $group) {
    $group->post('/login', AuthController::class . ':login');
});