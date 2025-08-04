<?php
declare(strict_types=1);

namespace Tests;

use App\command\MacroRotateCommand;
use App\Game\ChangeVelocityInterface;
use App\Game\Coords;
use App\Game\RotatebleInterface;
use PHPUnit\Framework\TestCase;

final class MacroRotateCommandTest extends TestCase
{
    public function testExecute_WithMovable_ExecutesRotateAndChangeVelocity(): void
    {
        $rotatable = $this->createMock(RotatebleInterface::class);
        $movable = $this->createMock(ChangeVelocityInterface::class);

        $rotatable->method('getDirection')->willReturn(1);
        $rotatable->method('getAngularVelocity')->willReturn(1);
        $rotatable->method('getDirectionsNumber')->willReturn(4);

        $rotatable->expects($this->once())
            ->method('setDirection')
            ->with(2);

        $movable->expects($this->once())
            ->method('setVelocity')
            ->with($this->callback(fn($coords) => $coords instanceof Coords));

        $command = new MacroRotateCommand($rotatable, $movable);
        $command->execute();
    }

    public function testExecute_WithoutMovable_OnlyRotates(): void
    {
        $rotatable = $this->createMock(RotatebleInterface::class);

        $rotatable->method('getDirection')->willReturn(1);
        $rotatable->method('getAngularVelocity')->willReturn(1);
        $rotatable->method('getDirectionsNumber')->willReturn(4);

        $rotatable->expects($this->once())
            ->method('setDirection')
            ->with(2);

        $command = new MacroRotateCommand($rotatable, null);
        $command->execute();

        $this->assertTrue(true); // Просто проверяем, что исключений нет
    }
}