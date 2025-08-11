<?php

namespace App\IoC;

/**
 * @template T
 */
interface CommandInterface
{
    /**
     * @return T
     */
    public function execute(): mixed;
}