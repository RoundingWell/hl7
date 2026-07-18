<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use DateTimeImmutable;
use Override;
use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\Exception\InvalidDate;

/**
 * Date
 *
 * Represents a YYYY[MM[DD]] date.
 */
final class DT extends AbstractPrimitive
{
    private const string PATTERN = '/^(\d{4})(\d{2})?(\d{2})?$/';

    private ?DateTimeImmutable $date = null;
    private ?string $format = null;

    public function getDateTime(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    #[Override]
    public function setValue(string $value): void
    {
        parent::setValue($value);

        if ($value === '') {
            $this->date = null;
            $this->format = null;

            return;
        }

        $matches = [];

        if (!preg_match(self::PATTERN, $value, $matches)) {
            throw InvalidDate::invalidValue($value);
        }

        // Prefix the format with ! to force all elements to start at zero.
        $format = '!Y';
        if (isset($matches[2])) { // @mago-expect lint:no-isset
            $format .= 'm';
        }
        if (isset($matches[3])) { // @mago-expect lint:no-isset
            $format .= 'd';
        }

        $dt = DateTimeImmutable::createFromFormat($format, $value);

        if (!$dt || $this->hasDateTimeErrors()) {
            throw InvalidDate::invalidValue($value);
        }

        $this->date = $dt;
        $this->format = $format;
    }

    private function hasDateTimeErrors(): bool
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
