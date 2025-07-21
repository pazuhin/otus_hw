<?php
declare(strict_types=1);

namespace App\Game;

final readonly class Move
{
    public function __construct(
        private MovableInterface $mObject,
    ) { }

    public function execute(): void
    {
        $newX = $this->mObject->getPosition()->getX() + $this->mObject->getVelocity()->getX();
        $newY = $this->mObject->getPosition()->getY() + $this->mObject->getVelocity()->getY();
        $this->mObject->setPosition(new Coords($newX, $newY));
    }
}