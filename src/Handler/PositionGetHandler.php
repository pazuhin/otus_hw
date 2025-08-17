<?php
declare(strict_types=1);

namespace App\Handler;

use App\Game\Coords;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: ['spaceship.method_handler'])]
final class PositionGetHandler implements HandlerInterface
{
    public function getKey(): string
    {
        return 'Spaceship.Operations.IMovable:position.get';
    }

    public function handle(object $obj, mixed ...$args): Coords
    {
        return $obj->position;
    }
}
