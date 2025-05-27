<?php

use App\Controllers\AuthController;
use App\Controllers\ReservationController;
use Slim\Routing\RouteCollectorProxy;

if(!isset($app)) {
    exit();
}

$app->group('/API/v1', function(RouteCollectorProxy $group) {
    $group->post('/login', AuthController::class . ':login');
    $group->post('/refresh', AuthController::class . ':refresh');

    $group->post('/reservation/add', ReservationController::class . ':addReservation');
});