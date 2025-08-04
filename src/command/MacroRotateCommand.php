<?php
declare(strict_types=1);

namespace App\command;

use App\Game\ChangeVelocityInterface;
use App\Game\RotatebleInterface;

final readonly class MacroRotateCommand implements CommandInterface
{
    public function __construct(
        private RotatebleInterface $rotatable,
        private ?ChangeVelocityInterface $movable = null,
    ) {}

    public function execute(): void
    {
        (new RotateCommand($this->rotatable))->execute();

        if ($this->movable !== null) {
            (new ChangeVelocityCommand($this->rotatable, $this->movable))->execute();
        }
    }
}