<?php
declare(strict_types=1);

namespace Tests;

use App\command\ChangeVelocityCommand;
use App\Game\ChangeVelocityInterface;
use App\Game\Coords;
use App\Game\RotatebleInterface;
use PHPUnit\Framework\TestCase;

final class ChangeVelocityTest extends TestCase
{
    public function testChangeVelocityWhenMovable()
    {
        $rotatable = $this->createMock(RotatebleInterface::class);
        $rotatable->method('getDirection')->willReturn(1);

        $movable = $this->createMock(ChangeVelocityInterface::class);
        $movable->expects($this->once())
            ->method('setVelocity')
            ->with($this->callback(fn($coords) => $coords instanceof Coords));

        $cmd = new ChangeVelocityCommand($rotatable, $movable);
        $cmd->execute();
    }

    public function testChangeVelocityWhenNotMovable()
    {
        $rotatable = $this->createMock(RotatebleInterface::class);
        $rotatable->method('getDirection')->willReturn(1);

        $cmd = new ChangeVelocityCommand($rotatable, null);

        $cmd->execute();
        $this->assertTrue(true);
    }
}