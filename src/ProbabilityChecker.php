<?php

declare(strict_types=1);

namespace Robier;

final class ProbabilityChecker
{
    private int|float $percentage;
    private int $maxRandomNumber;

    public function __construct(int|float $percentage)
    {
        $this->percentage = $percentage;

        $maxRandomNumber = 99;
        if ($percentage < 1) {
            $maxRandomNumber = (int)str_repeat('9', strlen((string)$percentage) - strpos((string)$percentage, '.') + 1);
        }

        $this->maxRandomNumber = $maxRandomNumber;
    }

    /**
     * Rolls the probability dice.
     */
    public function roll(): bool
    {
        if ($this->isCertian()) {
            return true;
        }

        if ($this->isImpossible()) {
            return false;
        }

        // Generate a random number between 0 and $maxRandomNumber
        $randomNumber = mt_rand(0, $this->maxRandomNumber);

        // Check if the random number falls below the threshold
        return $randomNumber < $this->percentage;
    }

    /**
     * Checks if "roll" method will always return true, percentage is equal or
     * greater than 100%.
     */
    public function isCertian(): bool
    {
        return $this->percentage >= 100;
    }

    /**
     * Checks if "roll" method will always return false, percentage is equal or
     * lower than 0%.
     */
    public function isImpossible(): bool
    {
        return $this->percentage <= 0;
    }

    /**
     * Checks if "roll" method will return random results, percentage is between
     * 0% and 100%.
     */
    public function isPossible(): bool
    {
        return !$this->isCertian() && !$this->isImpossible();
    }

    /**
     * Factory that returns an instance that will always return true on running "roll" method.
     */
    public static function always(): self
    {
        return new self(100);
    }

    /**
     * Factory that returns an instance that will always return false on running "roll" method.
     */
    public static function never(): self
    {
        return new self(0);
    }
}
