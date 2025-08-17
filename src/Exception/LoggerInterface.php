<?php

namespace App\Exception;

interface LoggerInterface
{
    public function log(string $errorMessage): void;
}
