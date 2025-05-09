<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

// Define the /API/v1 route
$app->get('/API/v1', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Welcome to API v1!");
    return $response;
});

$app->run();