<?php

namespace App\Game;

interface MovableInterface
{
    public function getPosition(): Coords;

    public function getVelocity(): Coords;

    public function setPosition(Coords $newPosition);
}