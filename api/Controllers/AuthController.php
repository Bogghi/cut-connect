<?php

namespace App\Controllers;

use App\Utility\_DataAccess;
use App\Utility\Result;
use App\Utility\TokenGenerator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    private _DataAccess $dataAccess;

    public function __construct(_DataAccess $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    public function login(Request $request, Response $response, array $args): Response
    {

        $result = new Result();
        $requestBody = $request->getParsedBody();

        if(isset($requestBody['username']) && isset($requestBody['password'])) {

            $username = $requestBody['username'];
            $password = $requestBody['password'];
            $hashPassword = hash('sha256', $password);

            $qRes = $this->dataAccess->get(
                table: "users",
                args: [
                    "user_name" => $username,
                    "password" => $hashPassword
                ],
                single: true,
                fields: [
                    'user_id',
                    'email',
                    'user_name'
                ],
            );

            if($qRes && count($qRes) > 0) {

                $tokenRes = TokenGenerator::generateToken(
                    userId: $qRes['user_id'],
                    email: $qRes['email'],
                    userName: $qRes['user_name'],
                );

                $result->setSuccessResult([
                    'token' => $tokenRes['token'],
                    'user' => $qRes
                ]);

                $this->dataAccess->add(
                    table: 'users_oauth_token',
                    requestData: [
                        'user_id' => $qRes['user_id'],
                        'token' => $tokenRes['token'],
                        'issued_at' => $tokenRes['iat'],
                        'expires_at' => $tokenRes['exp'],
                    ]
                );
            }
            else {
                $result->setInvalidParameters($requestBody);
            }

        }
        else {
            $result->setInvalidParameters();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }
}