<?php

declare(strict_types=1);

namespace Robier\Tests;

use Generator;
use PHPUnit\Framework\TestCase;
use Robier\ProbabilityChecker;

/**
 * @coversNothing
 */
final class ProbabilityCheckerTest extends TestCase
{
    /**
     * @covers \Robier\ProbabilityChecker::always
     * @covers \Robier\ProbabilityChecker::isCertian
     * @covers \Robier\ProbabilityChecker::isImpossible
     * @covers \Robier\ProbabilityChecker::isPossible
     * @covers \Robier\ProbabilityChecker::roll
     */
    public function testAlwaysFactory(): void
    {
        $checker = ProbabilityChecker::always();
        for ($i = 0; $i < 100; $i++) {
            self::assertTrue($checker->isCertian());
            self::assertFalse($checker->isImpossible());
            self::assertFalse($checker->isPossible());
            self::assertTrue($checker->roll());
        }
    }

    /**
     * @covers \Robier\ProbabilityChecker::never
     * @covers \Robier\ProbabilityChecker::isCertian
     * @covers \Robier\ProbabilityChecker::isImpossible
     * @covers \Robier\ProbabilityChecker::isPossible
     * @covers \Robier\ProbabilityChecker::roll
     */
    public function testNeverFactory(): void
    {
        $checker = ProbabilityChecker::never();
        for ($i = 0; $i < 100; $i++) {
            self::assertFalse($checker->isCertian());
            self::assertTrue($checker->isImpossible());
            self::assertFalse($checker->isPossible());
            self::assertFalse($checker->roll());
        }
    }

    public function isCertianDataProvider(): Generator
    {
        yield 'More than 100%' => [200];
        yield 'Exactly 100%' => [100];
        yield 'Exactly 100.0%' => [100.0];
    }

    /**
     * @covers \Robier\ProbabilityChecker::__construct
     * @covers \Robier\ProbabilityChecker::isCertian
     * @covers \Robier\ProbabilityChecker::isImpossible
     * @covers \Robier\ProbabilityChecker::isPossible
     * @covers \Robier\ProbabilityChecker::roll
     *
     * @dataProvider isCertianDataProvider
     */
    public function testIsCertian(int|float $percentage): void
    {
        $checker = new ProbabilityChecker($percentage);
        for ($i = 0; $i < 100; $i++) {
            // Test multiple times to make sure that the random number generation
            // is always the same
            self::assertTrue($checker->isCertian());
            self::assertFalse($checker->isImpossible());
            self::assertFalse($checker->isPossible());
            self::assertTrue($checker->roll());
        }
    }

    public function isImpossibleDataProvider(): Generator
    {
        yield 'Less than 0%' => [-15];
        yield 'Exactly 0%' => [0];
        yield 'Exactly 0.0%' => [0.0];
    }

    /**
     * @covers \Robier\ProbabilityChecker::__construct
     * @covers \Robier\ProbabilityChecker::isCertian
     * @covers \Robier\ProbabilityChecker::isImpossible
     * @covers \Robier\ProbabilityChecker::isPossible
     * @covers \Robier\ProbabilityChecker::roll
     *
     * @dataProvider isImpossibleDataProvider
     */
    public function testIsImpossible(int|float $percentage): void
    {
        $checker = new ProbabilityChecker($percentage);
        for ($i = 0; $i < 100; $i++) {
            // Test multiple times to make sure that the random number generation
            // is always the same
            self::assertFalse($checker->isCertian());
            self::assertTrue($checker->isImpossible());
            self::assertFalse($checker->isPossible());
            self::assertFalse($checker->roll());
        }
    }

    public function rollDataProvider(): Generator
    {
        yield '50%' => [50];
        yield '99%' => [99];
        yield '1%' => [1];
        yield '100%' => [100];
        yield '0%' => [0];
        yield '70%' => [70];
        yield '33%' => [33];
        yield '0.1%' => [0.1];
        yield '0.001%' => [0.001];
    }

    /**
     * @covers \Robier\ProbabilityChecker::__construct
     * @covers \Robier\ProbabilityChecker::isCertian
     * @covers \Robier\ProbabilityChecker::isImpossible
     * @covers \Robier\ProbabilityChecker::isPossible
     * @covers \Robier\ProbabilityChecker::roll
     *
     * @dataProvider rollDataProvider
     */
    public function testRoll(int|float $percentage): void
    {
        $checker = new ProbabilityChecker($percentage);

        $success = 0;
        for ($i = 0; $i < 100000; $i++) {
            $rollResult = $checker->roll();
            if ($percentage === 100) {
                self::assertTrue($checker->isCertian());
                self::assertFalse($checker->isImpossible());
                self::assertFalse($checker->isPossible());
            } elseif ($percentage === 0) {
                self::assertFalse($checker->isCertian());
                self::assertTrue($checker->isImpossible());
                self::assertFalse($checker->isPossible());
            } else {
                self::assertFalse($checker->isCertian());
                self::assertFalse($checker->isImpossible());
                self::assertIsBool($rollResult);
            }

            $success += (int)$checker->roll();
        }

        $successPercentage = round($success / 1000);

        $delta = 2;
        if($percentage < 1) {
            $delta = $percentage * 2;
        }

        self::assertEqualsWithDelta($percentage, $successPercentage, $delta);
    }
}
