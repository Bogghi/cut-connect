<?php

use App\Controllers\AuthController;
use App\Controllers\ReservationController;
use Slim\Routing\RouteCollectorProxy;

if(!isset($app)) {
    exit();
}

$app->group('/API/v1', function(RouteCollectorProxy $group) {
    // authentication endpoints
    $group->post('/login', AuthController::class . ':login');
    $group->post('/refresh', AuthController::class . ':refresh');

    // single reservation endpoints
    $group->post('/reservation/add', ReservationController::class . ':addReservation');

    // reservations endpoints
    $group->post('/reservations/get', ReservationController::class . ':getReservations');
});