<?php
declare(strict_types=1);

namespace App\Adapter;

use App\Game\Coords;
use App\Game\MovableInterface;
use App\Resolver\SpaceshipOperationsResolver;

final readonly class MovableAdapter implements MovableInterface
{
    public function __construct(
        private object                      $obj,
        private SpaceshipOperationsResolver $resolver
    ) {}

    public function getPosition(): Coords
    {
        return $this->resolver->resolve('Spaceship.Operations.IMovable:position.get', $this->obj);
    }

    public function setPosition(Coords $newPosition): mixed
    {
        $this->resolver->resolve('Spaceship.Operations.IMovable:position.set', $this->obj, $newPosition);

        return null;
    }

    public function getVelocity(): Coords
    {
        return $this->resolver->resolve('Spaceship.Operations.IMovable:velocity.get', $this->obj);
    }
}
