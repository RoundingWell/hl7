<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use DateTimeImmutable;
use RoundingWell\HL7\Exception\InvalidDateTime;

final class LazyDateTime
{
    use CanCheckDateTimeErrors;

    // HL7 timestamp format YYYY[MM[DD[HH[MM[SS[.S{1,4}]]]]]][+/-ZZZZ]
    private const string REGEX = '/^(\d{4})(\d{2})?(\d{2})?(\d{2})?(\d{2})?(\d{2})?(?:\.(\d{1,4}))?([+-]\d{4})?$/';

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
