<?php
declare(strict_types=1);

namespace App\Adapter;

use App\Resolver\SpaceshipOperationsResolver;

final readonly class AdapterGenerator
{
    public function __construct(
        private SpaceshipOperationsResolver $resolver
    ) {}

    /**
     * @template T of object
     * @param class-string<T> $interface
     * @param object $obj
     * @return T
     */
    public function generate(string $interface, object $obj): object
    {
        return new class($obj, $this->resolver, $interface) {
            public function __construct(
                private readonly object $obj,
                private readonly SpaceshipOperationsResolver $resolver,
                private readonly string $interface
            ) {}

            public function __call(string $method, array $args): mixed
            {
                $key = $this->interface . ':' . $method;
                return $this->resolver->resolve($key, $this->obj, ...$args);
            }
        };
    }
}
