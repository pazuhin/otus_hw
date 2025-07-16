<?php

namespace Tests;

use App\QuadraticSolver;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuadraticSolverTest extends TestCase
{
    #[DataProvider('noRootCases')]
    public function testNoRoots(float $a, float $b, float $c): void
    {
        $solver = new QuadraticSolver();
        $result = $solver->solve($a, $b, $c);
        $this->assertSame([], $result);
    }

    public static function noRootCases(): array
    {
        return [
            'x^2 + 1 = 0' => [1.0, 0.0, 1.0],
            'x^2 + 2x + 10 = 0' => [1.0, 2.0, 10.0],
        ];
    }

    #[DataProvider('oneRootCases')]
    public function testDoubleRoot(float $a, float $b, float $c): void
    {
        $solver = new QuadraticSolver();
        $result = $solver->solve($a, $b, $c);
        $this->assertCount(1, $result);
    }

    public static function oneRootCases(): array
    {
        return [
            'x^2 + 2x + 1 = 0' => [1.0, 2.0, 1.0],
            'x^2 - 2x + 1 = 0' => [1.0, -2.0, 1.0],
        ];
    }

    #[DataProvider('twoRootsCases')]
    public function testTwoRoots(float $a, float $b, float $c, array $expected): void
    {
        $solver = new QuadraticSolver();
        $result = $solver->solve($a, $b, $c);
        sort($result);
        sort($expected);
        $this->assertCount(2, $result);
        $this->assertEqualsCanonicalizing($expected, $result);
    }

    public static function twoRootsCases(): array
    {
        return [
            'x^2 - 1 = 0' => [1.0, 0.0, -1.0, [-1.0, 1.0]],
            'x^2 - 5x + 6 = 0' => [1.0, -5.0, 6.0, [2.0, 3.0]],
        ];
    }

    #[DataProvider('invalidCoefficientCases')]
    public function testInvalidCoefficients(float $a, float $b, float $c): void
    {
        $this->expectException(InvalidArgumentException::class);
        $solver = new QuadraticSolver();
        $solver->solve($a, $b, $c);
    }

    public static function invalidCoefficientCases(): array
    {
        return [
            'a = 0' => [0.0, 1.0, 1.0],
            'a = INF' => [INF, 1.0, 1.0],
            'b = NAN' => [1.0, NAN, 1.0],
            'c = -INF' => [1.0, 1.0, -INF],
        ];
    }
}
