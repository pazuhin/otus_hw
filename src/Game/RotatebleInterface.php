<?php

namespace App\Game;

interface RotatebleInterface
{
    public function getDirection(): int;

    public function getAngularVelocity(): int;

    public function getDirectionsNumber(): int;

    public function setDirection(int $direction): void;
}