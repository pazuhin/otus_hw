<?php

namespace App\Game;

interface ChangeVelocityInterface
{
    public function setVelocity(Coords $coords): void;
}