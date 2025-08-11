<?php

namespace App\IoC;

/**
 * @template T
 * @psalm-api
 */
interface CommandInterface
{
    /**
     * @return T
     */
    public function execute(): mixed;
}