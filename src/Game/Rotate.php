<?php
declare(strict_types=1);

namespace App\Game;

final class Rotate
{
    private RotatebleInterface $r;

    public function __construct(RotatebleInterface $r)
    {
        $this->r = $r;
    }

    public function execute(): void
    {
        $raw = $this->r->getDirection() + $this->r->getAngularVelocity();
        $mod = $this->r->getDirectionsNumber();
        $newDirection = (($raw % $mod) + $mod) % $mod;

        $this->r->setDirection($newDirection);
    }
}