<?php

namespace App\Game;

interface BurnFuelableInterface
{
    public function getFuelCount(): float;

    public function getFuelVelocity(): float;

    public function setFuelCount(float $fuelCount): void;
}