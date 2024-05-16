<?php

declare(strict_types=1);

namespace Robier\Tests;

use Generator;
use PHPUnit\Framework\TestCase;
use Robier\ProbabilityChecker;

/**
 * @covers ProbabilityChecker
 */
final class ProbabilityCheckerTest extends TestCase
{
    public function testAlwaysFactory(): void
    {
        $checker = ProbabilityChecker::always();
        for ($i = 0; $i < 100; $i++) {
            self::assertTrue($checker->isCertian());
            self::assertFalse($checker->isImpossible());
            self::assertTrue($checker->roll());
        }
    }

    public function testNeverFactory(): void
    {
        $checker = ProbabilityChecker::never();
        for ($i = 0; $i < 100; $i++) {
            self::assertFalse($checker->isCertian());
            self::assertTrue($checker->isImpossible());
            self::assertFalse($checker->roll());
        }
    }

    public function isCertianDataProvider(): Generator
    {
        yield 'More than 100%' => [200];
        yield 'Exactly 100%' => [100];
    }

    /**
     * @dataProvider isCertianDataProvider
     */
    public function testIsCertian(int $percentage): void
    {
        $checker = new ProbabilityChecker($percentage);
        for ($i = 0; $i < 100; $i++) {
            // Test multiple times to make sure that the random number generation
            // is always the same
            self::assertTrue($checker->isCertian());
            self::assertFalse($checker->isImpossible());
            self::assertTrue($checker->roll());
        }
    }

    public function isImpossibleDataProvider(): Generator
    {
        yield 'Less than 0%' => [-15];
        yield 'Exactly 0%' => [0];
    }

    /**
     * @dataProvider isImpossibleDataProvider
     */
    public function testIsImpossible(int $percentage): void
    {
        $checker = new ProbabilityChecker($percentage);
        for ($i = 0; $i < 100; $i++) {
            // Test multiple times to make sure that the random number generation
            // is always the same
            self::assertFalse($checker->isCertian());
            self::assertTrue($checker->isImpossible());
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
    }

    /**
     * @dataProvider rollDataProvider
     */
    public function testRoll(int $percentage): void
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

        self::assertEqualsWithDelta($percentage, $successPercentage, 2);
    }
}
