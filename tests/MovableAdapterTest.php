<?php
declare(strict_types=1);

namespace Tests;

use App\Adapter\MovableAdapter;
use App\Game\Coords;
use App\Handler\HandlerInterface;
use App\Resolver\SpaceshipOperationsResolver;
use PHPUnit\Framework\TestCase;

final class MovableAdapterTest extends TestCase
{
    public function setUp(): void
    {
        $this->spaceship = new class {
            public Coords $position;
            public Coords $velocity;

            public function __construct()
            {
                $this->position = new Coords(1, 2);
                $this->velocity = new Coords(0, 1);
            }
        };

        $handlers = [
            new class implements HandlerInterface {
                public function getKey(): string { return 'Spaceship.Operations.IMovable:position.get'; }
                public function handle(object $obj, mixed ...$args): Coords { return $obj->position; }
            },
            new class implements HandlerInterface {
                public function getKey(): string { return 'Spaceship.Operations.IMovable:position.set'; }
                public function handle(object $obj, mixed ...$args): mixed { $obj->position = $args[0]; return null;}
            },
            new class implements HandlerInterface {
                public function getKey(): string { return 'Spaceship.Operations.IMovable:velocity.get'; }
                public function handle(object $obj, mixed ...$args): Coords { return $obj->velocity; }
            },
        ];

        $resolver = new SpaceshipOperationsResolver($handlers);
        $this->adapter = new MovableAdapter($this->spaceship, $resolver);
    }

    public function testGetPosition(): void
    {
        $pos = $this->adapter->getPosition();
        $this->assertInstanceOf(Coords::class, $pos);
        $this->assertSame(1, $pos->getX());
        $this->assertSame(2, $pos->getY());
    }

    public function testSetPosition(): void
    {
        $newVector = new Coords(10, 20);
        $this->adapter->setPosition($newVector);

        $this->assertSame($newVector, $this->spaceship->position);
    }

    public function testGetVelocity(): void
    {
        $vel = $this->adapter->getVelocity();
        $this->assertInstanceOf(Coords::class, $vel);
        $this->assertSame(0, $vel->getX());
        $this->assertSame(1, $vel->getY());
    }
}
