<?php
declare(strict_types=1);

namespace App\command;

use App\Game\RotatebleInterface;

final readonly class RotateCommand implements CommandInterface
{
    public function __construct(
        private RotatebleInterface $rotatable,
    ) {}

    public function execute(): void
    {
        $raw = $this->rotatable->getDirection() + $this->rotatable->getAngularVelocity();
        $mod = $this->rotatable->getDirectionsNumber();
        $newDirection = (($raw % $mod) + $mod) % $mod;

        $this->rotatable->setDirection($newDirection);
    }
}