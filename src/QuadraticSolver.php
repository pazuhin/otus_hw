<?php
declare(strict_types=1);

namespace App;

use InvalidArgumentException;

final class QuadraticSolver
{
    private const EPSILON = 1e-10;

    public function solve(float $a, float $b, float $c): array
    {
        if (!is_finite($a) || !is_finite($b) || !is_finite($c)) {
            throw new InvalidArgumentException("Коэффициенты должны быть конечными числами.");
        }

        if (abs($a) < self::EPSILON) {
            throw new InvalidArgumentException("Коэффициент 'a' не должен быть равен нулю.");
        }

        $discriminant = $b * $b - 4 * $a * $c;

        if ($discriminant > self::EPSILON) {
            $sqrtD = sqrt($discriminant);
            return [(-$b + $sqrtD) / (2 * $a), (-$b - $sqrtD) / (2 * $a)];
        } elseif (abs($discriminant) < self::EPSILON) {
            return [-$b / (2 * $a)];
        }

        return [];
    }
}