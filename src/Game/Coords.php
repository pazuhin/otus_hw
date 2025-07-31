<?php
declare(strict_types=1);

namespace App\Game;

final readonly class Coords
{
    public function __construct(
        private int $x,
        private int $y,
    ){ }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function toArray(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
        ];
    }
}