<?php

namespace App\Utility;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

abstract class TokenGenerator
{
    private const string JWT_SECRET = JWT_SECRET;

    static public function generateToken(int $userId, string $email, string $userName): string
    {
        $payload = [
            'iss'  => 'ibarbierilissone.it', // Issuer
            'aud'  => 'ibarbierilissone.it',    // Audience
            'iat'  => time(),            // Issued At: current timestamp
            'exp'  => time() + (60 * 60), // Expiration Time: 1 hour from now
            'data' => [                  // Custom data
                'userId' => $userId,
                'username' => $userName,
                'role' => $email
            ]
        ];

        return JWT::encode($payload, self::JWT_SECRET, 'HS256');
    }
}