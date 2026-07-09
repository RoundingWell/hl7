<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use DateTimeImmutable;
use ReflectionProperty;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidDateTime;

/**
 * Date
 *
 * Represents a YYYY[MM[DD]] date.
 */
final class DT implements Type, \Stringable
{
    private const string PATTERN = '/^(\d{4})(\d{2})?(\d{2})?$/';

    private ?DateTimeImmutable $date = null;
    private ?string $format = null;
    private string $value;

    public function hasValue(): bool
    {
        // @mago-expect analysis:unhandled-thrown-type
        return new ReflectionProperty($this, 'value')->isInitialized($this);
    }

    public function setValue(string $value): void
    {
        if ($value === '') {
            $this->date = null;

            unset($this->format);
            unset($this->value);

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

        // @mago-expect analysis:invalid-property-assignment-value
        $this->date = DateTimeImmutable::createFromFormat($format, $value);
        $this->format = $format;
        $this->value = $value;
    }

    #[\Override]
    public function setRaw(Encoding $encoding, string $value, int $depth = 0): void
    {
        $this->setValue($encoding->decode($value));
    }

    public function getValue(): string
    {
        if ($this->hasValue()) {
            return $this->value;
        }

        return '';
    }

    public function getDateTime(): ?DateTimeImmutable
    {
        if ($this->hasValue()) {
            return $this->date;
        }

        return null;
    }

    public function getFormat(): ?string
    {
        if ($this->hasValue()) {
            return $this->format;
        }

        return null;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getValue();
    }
}
