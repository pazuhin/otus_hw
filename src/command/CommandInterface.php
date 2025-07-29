<?php

namespace App\command;

interface CommandInterface
{
    public function execute(): void;
}