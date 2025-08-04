<?php
declare(strict_types=1);

namespace App\command;


use App\Game\ChangeVelocityInterface;
use App\Game\Coords;
use App\Game\RotatebleInterface;

final readonly class ChangeVelocityCommand implements CommandInterface
{
    public function __construct(
        private RotatebleInterface $rotatable,
        private ?ChangeVelocityInterface $movable = null,
    ) {}

    public function execute(): void
    {
        if ($this->movable === null) {
            return;
        }

        $newVelocity = $this->calculateNewVelocity();
        $this->movable->setVelocity($newVelocity);
    }

    private function calculateNewVelocity(): Coords
    {
        // непонятно как расчитывать вектор скорости, чатГпт помог с этим)
        $direction = $this->rotatable->getDirection();
        $angle = deg2rad($direction * 90); // пример, если 4 направления

        $x = (int) round(cos($angle));
        $y = (int) round(sin($angle));

        return new Coords($x, $y);
    }
}