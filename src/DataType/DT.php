<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use DateTimeImmutable;
use Override;
use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\Exception\InvalidDateTime;

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

        // A date-only value matching PATTERN always builds, but createFromFormat is typed
        // DateTimeImmutable|false, so the (unreachable) failure branch is guarded inline.
        $dt = DateTimeImmutable::createFromFormat($format, $value);
        $this->date = $dt === false ? throw InvalidDateTime::invalidValue($value) : $dt;
        $this->format = $format;
    }
}
