<?php
declare(strict_types=1);

namespace Tests;

use App\Adapter\Factory\MovableAdapterFactory;
use App\Adapter\MovableAdapter;
use App\Resolver\SpaceshipOperationsResolver;
use PHPUnit\Framework\TestCase;

final class MovableAdapterFactoryTest extends TestCase
{
    public function testCreateReturnsMovableAdapter(): void
    {
        $resolverMock = $this->createMock(SpaceshipOperationsResolver::class);
        $factory = new MovableAdapterFactory($resolverMock);
        $obj = new \stdClass();
        $adapter = $factory->create($obj);

        // Проверяем, что вернулся экземпляр MovableAdapter
        $this->assertInstanceOf(MovableAdapter::class, $adapter);

        // Проверяем, что адаптер хранит правильный объект (через Reflection)
        $reflection = new \ReflectionClass(MovableAdapter::class);
        $property = $reflection->getProperty('obj');
        $this->assertSame($obj, $property->getValue($adapter));

        // Проверяем, что адаптер хранит правильный resolver
        $resolverProperty = $reflection->getProperty('resolver');
        $this->assertSame($resolverMock, $resolverProperty->getValue($adapter));
    }
}
