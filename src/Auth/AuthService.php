<?php
declare(strict_types=1);

namespace App\Auth;

use InvalidArgumentException;

final readonly class AuthService
{
    public function __construct(
        private JWTService              $jwtService,
        private GameRepositoryInterface $gameRepository
    ) {}

    public function createGame(string $organizerId, array $participants): string
    {
        if (empty($participants)) {
            throw new InvalidArgumentException('Participants list cannot be empty');
        }

        if (!in_array($organizerId, $participants, true)) {
            throw new InvalidArgumentException('Organizer must be included in participants list');
        }

        $gameId = uniqid('game_', true);
        $game = new Game($gameId, $participants, $organizerId, time());
        
        $this->gameRepository->save($game);
        
        return $gameId;
    }

    public function generateTokenForGame(string $userId, string $gameId): string
    {
        $game = $this->gameRepository->findById($gameId);
        
        if ($game === null) {
            throw new InvalidArgumentException('Game not found');
        }

        if (!$game->isParticipant($userId)) {
            throw new InvalidArgumentException('User is not a participant of this game');
        }

        return $this->jwtService->generateToken($userId, $gameId);
    }

    public function validateGameAccess(string $token, string $gameId): bool
    {
        try {
            $tokenGameId = $this->jwtService->extractGameId($token);
            return $tokenGameId === $gameId;
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    public function getGameInfo(string $gameId): ?array
    {
        $game = $this->gameRepository->findById($gameId);
        return $game?->toArray();
    }
}
