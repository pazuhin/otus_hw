<?php
declare(strict_types=1);

namespace App\Auth;

use InvalidArgumentException;

interface GameRepositoryInterface
{
    public function save(Game $game): void;
    public function findById(string $gameId): ?Game;
    public function exists(string $gameId): bool;
}
