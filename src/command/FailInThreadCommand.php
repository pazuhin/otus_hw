<?php

namespace App\command;

class FailInThreadCommand implements CommandInterface
{
    public function execute(): void
    {
        throw new \Exception("Something went wrong in command");
    }
}