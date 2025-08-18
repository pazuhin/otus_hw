<?php

namespace App\Handler;


/**
 * @template TReturn
 */
interface HandlerInterface
{
    public function getKey(): string;
    /**
     * @param object $obj
     * @param mixed ...$args
     * @return TReturn
     */
    public function handle(object $obj, mixed ...$args): mixed;
}
