<?php

namespace App\command;

class MoveInThreadCommand implements CommandInterface
{
    public function execute(): void
    {
        echo "Moving..." . PHP_EOL;
    }
}