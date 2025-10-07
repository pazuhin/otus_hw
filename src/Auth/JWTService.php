<?php
declare(strict_types=1);

namespace App\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use InvalidArgumentException;

final class JWTService
{
    private const ALGORITHM = 'HS256';
    
    public function __construct(
        private readonly string $secretKey
    ) {}

    public function generateToken(string $userId, string $gameId, int $expirationTime = 3600): string
    {
        $payload = [
            'iss' => 'space-battle-auth-service',
            'aud' => 'space-battle-game-server',
            'iat' => time(),
            'exp' => time() + $expirationTime,
            'user_id' => $userId,
            'game_id' => $gameId,
        ];

        return JWT::encode($payload, $this->secretKey, self::ALGORITHM);
    }

    public function validateToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, self::ALGORITHM));
            return (array) $decoded;
        } catch (\Exception $e) {
            throw new InvalidArgumentException('Invalid JWT token: ' . $e->getMessage());
        }
    }

    public function extractGameId(string $token): string
    {
        $payload = $this->validateToken($token);
        
        if (!isset($payload['game_id'])) {
            throw new InvalidArgumentException('Game ID not found in token');
        }

        return $payload['game_id'];
    }

    public function extractUserId(string $token): string
    {
        $payload = $this->validateToken($token);
        
        if (!isset($payload['user_id'])) {
            throw new InvalidArgumentException('User ID not found in token');
        }

        return $payload['user_id'];
    }
}
