<?php

namespace App\Utility;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

abstract class TokenGenerator
{
    private const string JWT_SECRET = JWT_SECRET;

    static public function generateToken(int $userId, string $email, string $userName): array
    {
        $iat = time();
        $exp = $iat + (60 * 60); // 1 hour expiration
        $payload = [
            'iss'  => 'ibarbierilissone.it', // Issuer
            'aud'  => 'ibarbierilissone.it',    // Audience
            'iat'  => $iat,            // Issued At: current timestamp
            'exp'  => $exp, // Expiration Time: 1 hour from now
            'data' => [                  // Custom data
                'userId' => $userId,
                'username' => $userName,
                'role' => $email
            ]
        ];
        $token = JWT::encode($payload, self::JWT_SECRET, 'HS256');

        return [
            'token' => $token,
            'iat' => $iat,
            'exp' => $exp,
        ];
    }
}