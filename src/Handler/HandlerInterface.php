<?php

namespace App\Handler;

interface HandlerInterface
{
    public function handle(object $obj, mixed ...$args): mixed;
}
