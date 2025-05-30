<?php

namespace App\Controllers;

use App\Utility\_DataAccess;
use App\Utility\Result;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController extends BaseController
{
    private _DataAccess $dataAccess;
    
    public function __construct(_DataAccess $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if($this->validateToken($request)) {

            $users = $this->dataAccess->get(
                table: "users",
                fields: [
                    'user_id',
                    'email',
                    'user_name'
                ],
            );

            $result->setSuccessResult([
                'users' => $users,
            ]);

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