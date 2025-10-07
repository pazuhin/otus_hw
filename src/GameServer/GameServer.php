<?php
declare(strict_types=1);

namespace App\GameServer;

use App\Auth\JWTService;
use InvalidArgumentException;

final readonly class GameServer
{
    public function __construct(
        private JWTService $jwtService
    ) {}

    public function processGameCommand(string $token, string $command, array $data = []): array
    {
        try {
            $payload = $this->jwtService->validateToken($token);
            
            $gameId = $payload['game_id'] ?? null;
            $userId = $payload['user_id'] ?? null;
            
            if (!$gameId || !$userId) {
                throw new InvalidArgumentException('Invalid token payload');
            }

            return [
                'success' => true,
                'game_id' => $gameId,
                'user_id' => $userId,
                'command' => $command,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
