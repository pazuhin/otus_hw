<?php
declare(strict_types=1);

namespace App\Handler;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: ['spaceship.method_handler'])]
final class PositionSetHandler implements HandlerInterface
{
    public function getKey(): string
    {
        return 'App\Game\MovableInterface:setPosition';
    }

    public function handle(object $obj, mixed ...$args): null
    {
        $obj->position = $args[0];

        return null;
    }
}
