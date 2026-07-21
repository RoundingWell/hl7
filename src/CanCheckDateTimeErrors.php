<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use DateTimeImmutable;

trait CanCheckDateTimeErrors
{
    private static function hasDateTimeErrors(): bool
    {
        $errors = DateTimeImmutable::getLastErrors();

        // DateTimeImmutable::createFromFormat silently rolls over out-of-range components
        // (e.g. month 13 becomes January of the next year, Feb 30 becomes March 2),
        // reporting them only as warnings while still returning a date.
        // DateTimeImmutable::getLastErrors() returns false when the parse was clean.
        // Reject any warning so a component outside its calendar range is
        // never accepted as a different, valid instant.
        return $errors !== false && $errors['warning_count'] > 0;
    }
}
