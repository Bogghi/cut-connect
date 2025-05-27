<?php

namespace App\Controllers;

use App\Utility\_DataAccess;
use App\Utility\Result;
use App\Utility\TokenGenerator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController extends BaseController
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

                $oauthRes = $this->dataAccess->get(
                    table: "users_oauth_token",
                    args: [
                        "user_id" => $qRes['user_id'],
                        "expires_at" => ">now",
                    ],
                    single: true,
                    fields: ['token','user_oauth_token_id']
                );

                if($oauthRes) {
                    $refreshTokenRes = $this->dataAccess->get(
                        table: "users_oauth_refresh_token",
                        args: [
                            "user_id" => $qRes['user_id'],
                            "expires_at" => ">now",
                        ],
                        single: true,
                        fields: ['refresh_token']
                    );
                    $token = $oauthRes['token'];
                    $refreshToken = $refreshTokenRes['refresh_token'];
                }
                else {
                    $tokenRes = TokenGenerator::generateToken(
                        userId: $qRes['user_id'],
                        email: $qRes['email'],
                        userName: $qRes['user_name'],
                    );
                    $token = $tokenRes['token'];

                    $this->dataAccess->add(
                        table: 'users_oauth_token',
                        requestData: [
                            'user_id' => "".$qRes['user_id'],
                            'token' => $tokenRes['token'],
                            'issued_at' => $tokenRes['iat'],
                            'expires_at' => $tokenRes['exp'],
                        ]
                    );

                    $refreshTokenRes = TokenGenerator::generateRefreshToken(
                        userId: $qRes['user_id'],
                        email: $qRes['email'],
                        userName: $qRes['user_name'],
                    );
                    $refreshToken = $refreshTokenRes['refreshToken'];

                    $this->dataAccess->add(
                        table: 'users_oauth_refresh_token',
                        requestData: [
                            'user_id' => "".$qRes['user_id'],
                            'refresh_token' => $refreshTokenRes['refreshToken'],
                            'issued_at' => $refreshTokenRes['iat'],
                            'expires_at' => $refreshTokenRes['exp'],
                        ]
                    );
                }

                $result->setSuccessResult([
                    'token' => $token,
                    'refreshToken' => $refreshToken,
                    'user' => $qRes,
                ]);
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

    public function refresh(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if($this->validateToken($request)) {

            $tokenRes = TokenGenerator::generateToken(
                userId: $this->userId,
                email: $this->email,
                userName: $this->username,
            );


            $this->dataAccess->customQuery(
                query: "delete from users_oauth_token where user_id = ?;",
                params: [$this->userId]
            );
            $this->dataAccess->add(
                table: 'users_oauth_token',
                requestData: [
                    'token' => $tokenRes['token'],
                    'issued_at' => $tokenRes['iat'],
                    'expires_at' => $tokenRes['exp'],
                    'user_id' => "".$this->userId,
                ],
            );

            $result->setSuccessResult(['token' => $tokenRes['token']]);

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