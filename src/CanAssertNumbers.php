<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use InvalidArgumentException;

trait CanAssertNumbers
{
    private function assertPositiveNumber(int $number): void
    {
        if ($number <= 0) {
            throw new InvalidArgumentException("Number must be 1 or greater, got {$number}");
        }
    }

    private function assertNaturalNumber(int $number): void
    {
        if ($number < 0) {
            throw new InvalidArgumentException("Number must be 0 or greater, got {$number}");
        }
    }
}
