<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use DateTimeImmutable;
use Override;
use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\Exception\InvalidDateTime;

/**
 * Date/Time
 *
 * Represents a YYYY[MM[DD[HH[MM[SS[.S{1,4}]]]]]][+/-ZZZZ] timestamp.
 */
final class DTM extends AbstractPrimitive
{
    private const string PATTERN = '/^(\d{4})(\d{2})?(\d{2})?(\d{2})?(\d{2})?(\d{2})?(?:\.(\d{1,4}))?([+-]\d{4})?$/';

    private ?DateTimeImmutable $dateTime = null;
    private ?string $format = null;

    public function getDateTime(): ?DateTimeImmutable
    {
        return $this->dateTime;
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
            $this->dateTime = null;
            $this->format = null;

            return;
        }

        $matches = [];

        if (!preg_match(self::PATTERN, $value, $matches)) {
            throw InvalidDateTime::invalidValue($value);
        }

        // Prefix the format with ! to force all elements to start at zero.
        $format = '!Y';
        if (isset($matches[2])) { // @mago-expect lint:no-isset
            $format .= 'm';
        }
        if (isset($matches[3])) { // @mago-expect lint:no-isset
            $format .= 'd';
        }
        if (isset($matches[4])) { // @mago-expect lint:no-isset
            $format .= 'H';
        }
        if (isset($matches[5])) { // @mago-expect lint:no-isset
            $format .= 'i';
        }
        if (isset($matches[6])) { // @mago-expect lint:no-isset
            $format .= 's';
        }
        if (isset($matches[7]) && $matches[7] !== '') { // @mago-expect lint:no-isset
            $format .= '.u';
        }
        if (isset($matches[8])) { // @mago-expect lint:no-isset
            $format .= 'O';
        }

        $dt = DateTimeImmutable::createFromFormat($format, $value);

        if (!$dt || $this->hasDateTimeErrors()) {
            throw InvalidDateTime::invalidValue($value);
        }

        $this->dateTime = $dt;
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
