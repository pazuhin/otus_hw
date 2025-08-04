<?php
declare(strict_types=1);

namespace Tests;

use App\command\RotateCommand;
use App\Game\RotatebleInterface;
use PHPUnit\Framework\TestCase;

final class RotateCommandTest extends TestCase
{
    public function testRotate(): void
    {
        $rotatable = $this->createMock(RotatebleInterface::class);
        $rotatable->method('getDirection')->willReturn(1);
        $rotatable->method('getAngularVelocity')->willReturn(2);
        $rotatable->method('getDirectionsNumber')->willReturn(4);

        $rotatable->expects($this->once())
            ->method('setDirection')
            ->with(3);

        (new RotateCommand($rotatable))->execute();
    }
}