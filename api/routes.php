<?php

use App\Controllers\AuthController;
use App\Controllers\ReservationController;
use App\Controllers\UserController;
use Slim\Routing\RouteCollectorProxy;

if(!isset($app)) {
    exit();
}

const BASE_ROUTE = '/API/v1';

$app->post(BASE_ROUTE.'/login', AuthController::class . ':login');
$app->get(BASE_ROUTE.'/refresh', AuthController::class . ':refresh');
$app->post(BASE_ROUTE.'/reservations/get', ReservationController::class . ':getReservations');

$app->group(BASE_ROUTE.'/reservation', function(RouteCollectorProxy $group) {

    $group->post('/add', ReservationController::class . ':addReservation');
    $group->post('/delete', ReservationController::class . ':deleteReservation');

});

$app->group(BASE_ROUTE.'/users', function(RouteCollectorProxy $group) {

    $group->get('/get', UserController::class . ':get');

});