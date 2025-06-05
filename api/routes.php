<?php

use App\Controllers\AuthController;
use App\Controllers\ReservationController;
use App\Controllers\UserController;
use App\Controllers\ServicesController;
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
    $group->post('/update', ReservationController::class . ':bottomSheet');

});

$app->group(BASE_ROUTE.'/users', function(RouteCollectorProxy $group) {

    $group->get('/get', UserController::class . ':get');

});

$app->get(BASE_ROUTE.'/services/get', ServicesController::class . ':getServices');
$app->group(BASE_ROUTE.'/service', function(RouteCollectorProxy $group) {

    $group->post('/add', ServicesController::class . ':addService');

});