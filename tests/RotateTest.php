<?php
declare(strict_types=1);

namespace Tests;

use App\Game\Rotate;
use App\Game\RotatebleInterface;
use DivisionByZeroError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class RotateTest extends TestCase
{
    #[DataProvider('rotationCases')]
    public function testRotation(int $direction, int $angularVelocity, int $directionsNumber, int $expectedNewDirection): void
    {
        $rObject = $this->createMock(RotatebleInterface::class);

        $rObject->method('getDirection')->willReturn($direction);
        $rObject->method('getAngularVelocity')->willReturn($angularVelocity);
        $rObject->method('getDirectionsNumber')->willReturn($directionsNumber);

        $rObject->expects($this->once())
            ->method('setDirection')
            ->with($expectedNewDirection);

        $rotate = new Rotate($rObject);
        $rotate->execute();
    }

    public static function rotationCases(): array
    {
        return [
            'clockwise positive'      => [2, 1, 4, 3],
            'wrap around positive'    => [3, 2, 4, 1],
            'counter-clockwise'       => [1, -1, 4, 0],
            'wrap around negative'    => [0, -1, 4, 3],
            'negative multiple wrap'  => [0, -5, 4, 3],
        ];
    }

    /**
     * Ошибка: невозможно прочитать направление
     */
    public function testFailsWhenPositionUnavailable(): void
    {
        $rObject = $this->createMock(RotatebleInterface::class);
        $rObject->method('getDirection')->willThrowException(new RuntimeException('Position unavailable'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Position unavailable');

        (new Rotate($rObject))->execute();
    }

    /**
     * Ошибка: деление на 0
     */
    public function testFailsWhenAngularVelocityUnavailable(): void
    {
        $rObject = $this->createMock(RotatebleInterface::class);
        $rObject->method('getDirectionsNumber')->willReturn(0);
        $rObject->method('getAngularVelocity')->willThrowException(new DivisionByZeroError('Angular Velocity unavailable'));

        $this->expectException(DivisionByZeroError::class);
        $this->expectExceptionMessage('Angular Velocity unavailable');

        (new Rotate($rObject))->execute();
    }

    /**
     * Ошибка: невозможно установить новую позицию
     */
    public function testFailsWhenSetPositionFails(): void
    {
        $rObject = $this->createMock(RotatebleInterface::class);
        $rObject->method('getDirectionsNumber')->willReturn(1);
        $rObject->method('setDirection')->willThrowException(new RuntimeException('Cannot set position'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot set position');

        (new Rotate($rObject))->execute();
    }
}