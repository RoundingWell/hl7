<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use DateTimeImmutable;
use RoundingWell\HL7\Exception\InvalidDate;

final class LazyDate
{
    use CanCheckDateTimeErrors;

    // HL7 date format YYYY[MM[DD]]
    // Each component is nested inside the previous one so a lower-precision component can
    // never appear without every higher-precision component before it (a day requires a
    // month), mirroring LazyDateTime.
    private const string REGEX = '/^(\d{4})(?:(\d{2})(\d{2})?)?$/';

    private ?string $format = null;
    private ?DateTimeImmutable $dateTime = null;

    public function __construct(
        private readonly string $value,
    ) {}

    public function getFormat(): string
    {
        if ($this->format) {
            return $this->format;
        }

        $matches = [];

        if (!preg_match(self::REGEX, $this->value, $matches)) {
            throw InvalidDate::invalidValue($this->value);
        }

        // Prefix the format with ! to prevent missing elements from inheriting current values.
        $format = '!Y';

        foreach ([2 => 'm', 3 => 'd'] as $index => $component) {
            $v = $matches[$index] ?? '';

            if ($v !== '') {
                $format .= $component;
            }
        }

        return $this->format = $format;
    }

    public function getDateTime(): DateTimeImmutable
    {
        if ($this->dateTime) {
            return $this->dateTime;
        }

        $dt = DateTimeImmutable::createFromFormat($this->getFormat(), $this->value);

        if (!$dt || self::hasDateTimeErrors()) {
            throw InvalidDate::invalidValue($this->value);
        }

        return $this->dateTime = $dt;
    }
}
