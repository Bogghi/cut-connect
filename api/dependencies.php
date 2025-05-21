<?php

use App\Utility\_DataAccess;
use App\Controllers\AuthController;
use Psr\Container\ContainerInterface;

if (!isset($container) || !$container instanceof ContainerInterface) {
    // Or throw an exception, depending on your error handling strategy
    die("Error: Dependency container not initialized. This file should be included by index.php.");
}

$container->set(_DataAccess::class, function (ContainerInterface $c) {
    return new _DataAccess();
});
$container->set(AuthController::class, function (ContainerInterface $c) {
    return new AuthController($c->get(_DataAccess::class));
});