<?php
declare(strict_types=1);

namespace App\IoC;

final class Definition
{
    /** @var callable */
    public $factory;

    /**
     * @param callable $factory Фабрика, создающая сервис.
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array $args
     * @return mixed
     */
    public function create(array $args = []): mixed
    {
        return ($this->factory)(...$args);
    }
}