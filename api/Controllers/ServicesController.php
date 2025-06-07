<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Utility\_DataAccess;
use App\Utility\Result;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ServicesController extends BaseController
{
    private _DataAccess $dataAccess;

    public function __construct(_DataAccess $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    public function getServices(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if($this->validateToken($request)) {

            $services = $this->dataAccess->get(
                table: 'services',
                args: ['deleted' => 0],
            );

            $result->setSuccessResult(['services' => $services]);

        }
        else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }

    public function addService(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if($this->validateToken($request)) {

            $requestBody = $request->getParsedBody();

            $service_name = $requestBody['service_name'] ?? null;
            $description = $requestBody['description'] ?? null;
            $price = $requestBody['price'] ?? null;
            $duration = $requestBody['duration'] ?? null;

            if($service_name && $price && $duration) {
                $service = [
                    'service_name' => $service_name,
                    'price' => $price,
                    'duration' => $duration
                ];
                if($description) {
                    $service['description'] = $description;
                }

                $insertId = $this->dataAccess->add(
                    table: 'services',
                    requestData: $service,
                );

                if($insertId) {
                    $result->setSuccessResult(['service_id' => $insertId]);
                }
                else {
                    $result->setGenericError();
                }
            }
            else {
                $result->setInvalidParameters();
            }

        }
        else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }

    public function deleteService(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if($this->validateToken($request)) {

            $requestBody = $request->getParsedBody();

            $service_id = $requestBody['service_id'] ?? null;

            if($service_id) {
                $updateResult = $this->dataAccess->update(
                    table: 'services',
                    args: ['service_id' => $service_id],
                    requestData: [
                        'deleted' => 1
                    ],
                );
                $result->setSuccessResult();
            }
            else {
                $result->setInvalidParameters();
            }

        }
        else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }

    public function updateService(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if($this->validateToken($request)) {

            $requestBody = $request->getParsedBody();

            $service_id = $requestBody['service_id'] ?? null;
            $service_name = $requestBody['service_name'] ?? null;
            $description = $requestBody['description'] ?? null;
            $price = $requestBody['price'] ?? null;
            $duration = $requestBody['duration'] ?? null;

            if($service_id && $service_name && $price && $duration) {

                $service = [
                    'service_name' => $service_name,
                    'price' => $price,
                    'duration' => $duration
                ];
                if($description) {
                    $service['description'] = $description;
                }

                $updateResult = $this->dataAccess->update(
                    table: 'services',
                    args: ['service_id' => $service_id],
                    requestData: $service,
                );

                if($updateResult) {
                    $result->setSuccessResult();
                }
                else {
                    $result->setGenericError();
                }

            }
            else {
                $result->setInvalidParameters();
            }


        }
        else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }
}