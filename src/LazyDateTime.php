<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use DateTimeImmutable;
use RoundingWell\HL7\Exception\InvalidDateTime;

final class LazyDateTime
{
    use CanCheckDateTimeErrors;

    // HL7 timestamp format YYYY[MM[DD[HH[MM[SS[.S{1,4}]]]]]][+/-ZZZZ]
    // Each component is nested inside the previous one so a lower-precision component can
    // never appear without every higher-precision component before it (e.g. a fractional
    // second requires seconds, seconds require minutes). This rejects gapped values that PHP
    // would otherwise reinterpret as a different, valid instant.
    private const string REGEX = <<<'PATTERN'
        /^
        (\d{4})                 # year
        (?: (\d{2})             # month
        (?: (\d{2})             # day
        (?: (\d{2})             # hour
        (?: (\d{2})             # minute
        (?: (\d{2})             # second
        (?: \. (\d{1,4}) )?     # fractional second
        )?)?)?)?)?
        ([+-]\d{4})?            # UTC offset
        $/x
        PATTERN;

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
            throw InvalidDateTime::invalidValue($this->value);
        }

        // Prefix the format with ! to prevent missing elements from inheriting current values.
        $format = '!Y';

        foreach ([2 => 'm', 3 => 'd', 4 => 'H', 5 => 'i', 6 => 's', 7 => '.u', 8 => 'O'] as $index => $component) {
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
            throw InvalidDateTime::invalidValue($this->value);
        }

        return $this->dateTime = $dt;
    }
}
