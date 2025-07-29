<?php

namespace App\exception;

interface LoggerInterface
{
    public function log(string $errorMessage): void;
}