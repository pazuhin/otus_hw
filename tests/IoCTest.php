<?php
declare(strict_types=1);

namespace Tests;

use App\IoC\IoC;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class IoCTest extends TestCase
{
    private IoC $ioc;

    protected function setUp(): void
    {
        $this->ioc = new IoC();
        $this->ioc->reset();
    }

    public function testRegisterAndResolve(): void
    {
        $this->ioc->resolve('IoC.Register', 'obj', fn() => new stdClass())->execute();
        $obj = $this->ioc->resolve('obj')->execute();
        $this->assertInstanceOf(stdClass::class, $obj);
    }

    public function testResolveWithArguments(): void
    {
        $this->ioc->resolve('IoC.Register', 'sum', fn(int $a, int $b): int => $a + $b)->execute();
        $sum = $this->ioc->resolve('sum', 10, 5)->execute();
        $this->assertSame(15, $sum);
    }

    public function testScopeIsolation(): void
    {
        // Регистрируем в глобальном скоупе
        $this->ioc->resolve('IoC.Register', 'val', fn() => 'global')->execute();

        // Создаем и переключаемся на новый скоуп
        $this->ioc->resolve('Scopes.New', 'scope1')->execute();
        $this->ioc->resolve('Scopes.Current', 'scope1')->execute();

        // В новом скоупе регистрируем другую реализацию
        $this->ioc->resolve('IoC.Register', 'val', fn() => 'scope1')->execute();

        $valScope = $this->ioc->resolve('val')->execute();
        $this->assertSame('scope1', $valScope);

        // Переключаемся обратно на глобальный
        $this->ioc->resolve('Scopes.Current', '_global')->execute();

        $valGlobal = $this->ioc->resolve('val')->execute();
        $this->assertSame('global', $valGlobal);
    }

    public function testExceptionIfServiceNotFound(): void
    {
        $this->expectException(RuntimeException::class);
        $this->ioc->resolve('nonexistent_service')->execute();
    }

    public function testScopeNewAndCurrentWithInvalidId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->ioc->resolve('Scopes.New', '')->execute();
    }

    public function testScopeCurrentCreatesScopeIfNotExists(): void
    {
        $this->ioc->resolve('Scopes.Current', 'myScope')->execute();

        // Проверяем, что scope создался и можно в нем регистрировать сервис
        $this->ioc->resolve('IoC.Register', 'foo', fn() => 'bar', 'myScope')->execute();
        $this->ioc->resolve('Scopes.Current', 'myScope')->execute();
        $res = $this->ioc->resolve('foo')->execute();

        $this->assertSame('bar', $res);
    }
}