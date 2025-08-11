<?php
declare(strict_types=1);

namespace App\IoC;

use Override;

/**
 * @template T
 * @implements CommandInterface<T>
 */
final class Command implements CommandInterface
{
    /** @var callable(): T|null */
    private $callable;

    /**
     * @param callable(): T|null $callable
     */
    public function __construct(?callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @return T
     * @throws \Exception
     */
    #[Override]
    public function execute(): mixed
    {
        if ($this->callable === null) {
            throw new \Exception('Не чего вызывать');
        }

        return ($this->callable)();
    }
}