<?php
declare(strict_types=1);

namespace Tests;

use App\Game\Coords;
use App\Game\MovableInterface;
use App\Game\Move;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MovingTest extends TestCase
{
    #[DataProvider('straightLine')]
    public function testStraightLine(Coords $position, Coords $velocity, Coords $newPosition): void
    {
        $mObject = $this->createMock(MovableInterface::class);
        $mObject->method('getPosition')->willReturn($position);
        $mObject->method('getVelocity')->willReturn($velocity);
        $mObject->expects($this->once())
            ->method('setPosition')
            ->with($this->callback(function(Coords $coords) use ($newPosition) {
                return $coords->toArray() === $newPosition->toArray();
            }));

        $move = new Move($mObject);
        $move->execute();
    }

    public static function straightLine(): array
    {
        return [
            '[12, 5] , [-7, 3] -> [5, 8]' => [new Coords(12,5), new Coords(-7,3), new Coords(5,8)],
            '[7, 3] , [1, 7] -> [8, 10]' => [new Coords(7,3), new Coords(1,7), new Coords(8,10)],
        ];
    }

    /**
     * Ошибка: невозможно прочитать позицию
     */
    public function testFailsWhenPositionUnavailable(): void
    {
        $mObject = $this->createMock(MovableInterface::class);
        $mObject->method('getPosition')->willThrowException(new RuntimeException('Position unavailable'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Position unavailable');

        (new Move($mObject))->execute();
    }

    /**
     * Ошибка: невозможно прочитать скорость
     */
    public function testFailsWhenVelocityUnavailable(): void
    {
        $mObject = $this->createMock(MovableInterface::class);
        $mObject->method('getPosition')->willReturn(new Coords(0, 0));
        $mObject->method('getVelocity')->willThrowException(new RuntimeException('Velocity unavailable'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Velocity unavailable');

        (new Move($mObject))->execute();
    }

    /**
     * Ошибка: невозможно установить новую позицию
     */
    public function testFailsWhenSetPositionFails(): void
    {
        $mObject = $this->createMock(MovableInterface::class);
        $mObject->method('getPosition')->willReturn(new Coords(1, 1));
        $mObject->method('getVelocity')->willReturn(new Coords(1, 1));
        $mObject->method('setPosition')->willThrowException(new RuntimeException('Cannot set position'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot set position');

        (new Move($mObject))->execute();
    }
}