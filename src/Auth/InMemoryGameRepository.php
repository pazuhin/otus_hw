<?php
declare(strict_types=1);

namespace App\Auth;

final class InMemoryGameRepository implements GameRepositoryInterface
{
    private array $games = [];

    public function save(Game $game): void
    {
        $this->games[$game->getId()] = $game;
    }

    public function findById(string $gameId): ?Game
    {
        return $this->games[$gameId] ?? null;
    }

    public function exists(string $gameId): bool
    {
        return isset($this->games[$gameId]);
    }
}
