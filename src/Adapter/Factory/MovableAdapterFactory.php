<?php
declare(strict_types=1);

namespace App\Adapter\Factory;

use App\Adapter\MovableAdapter;
use App\Resolver\SpaceshipOperationsResolver;

final readonly class MovableAdapterFactory
{
    public function __construct(
        private SpaceshipOperationsResolver $resolver
    ) {}

    public function create(object $obj): MovableAdapter
    {
        return new MovableAdapter($obj, $this->resolver);
    }
}
