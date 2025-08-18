<?php
declare(strict_types=1);

use App\Adapter\AdapterGenerator;
use App\Game\MovableInterface;
use App\Handler\HandlerInterface;
use App\Resolver\SpaceshipOperationsResolver;
use PHPUnit\Framework\TestCase;

class AdapterGeneratorTest extends TestCase
{
    public function testGenerateAdapterAndSetPosition(): void
    {
        /** @var list<HandlerInterface<mixed, array>> $handlers */
        $handlers = [
            new class implements HandlerInterface {
                public function getKey(): string { return 'App\Game\MovableInterface:setPosition'; }
                public function handle(object $obj, mixed ...$args): mixed { $obj->position = $args[0]; return null; }
            },
            new class implements HandlerInterface {
                public function getKey(): string { return 'App\Game\MovableInterface:getPosition'; }
                public function handle(object $obj, mixed ...$args): mixed { return $obj->position; }
            },
        ];

        $resolver = new SpaceshipOperationsResolver($handlers);
        $generator = new AdapterGenerator($resolver);

        $obj = new \stdClass();

        $adapter = $generator->generate(MovableInterface::class, $obj);

        $adapter->setPosition(new \App\Game\Coords(1, 2));
        $pos = $adapter->getPosition();

        $this->assertInstanceOf(\App\Game\Coords::class, $pos);
        $this->assertSame(1, $pos->getX());
        $this->assertSame(2, $pos->getY());
    }
}
