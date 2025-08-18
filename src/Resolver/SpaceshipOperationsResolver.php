<?php
declare(strict_types=1);

namespace App\Resolver;

use App\Handler\HandlerInterface;

class SpaceshipOperationsResolver
{
    /** @var array<string, HandlerInterface> */
    private array $handlersMap = [];

    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            $key = $handler->getKey();
            if (isset($this->handlersMap[$key])) {
                throw new \RuntimeException("Duplicate handler for key $key");
            }
            $this->handlersMap[$key] = $handler;
        }
    }

    public function resolve(string $key, object $obj, mixed ...$args): mixed
    {
        if (!isset($this->handlersMap[$key])) {
            throw new \RuntimeException("No handler for key $key");
        }
        return $this->handlersMap[$key]->handle($obj, ...$args);
    }

    public function getHandlers(): array
    {
        return $this->handlersMap;
    }
}
