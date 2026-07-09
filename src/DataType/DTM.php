<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use DateTimeImmutable;
use ReflectionProperty;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidDateTime;

/**
 * Date/Time
 *
 * Represents a YYYY[MM[DD[HH[MM[SS[.S{1,4}]]]]]][+/-ZZZZ] timestamp.
 */
final class DTM implements Type, \Stringable
{
    private const string PATTERN = '/^(\d{4})(\d{2})?(\d{2})?(\d{2})?(\d{2})?(\d{2})?(?:\.(\d{1,4}))?([+-]\d{4})?$/';

    private ?DateTimeImmutable $dateTime = null;
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
            $this->dateTime = null;

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
        if (isset($matches[4])) { // @mago-expect lint:no-isset
            $format .= 'H';
        }
        if (isset($matches[5])) { // @mago-expect lint:no-isset
            $format .= 'i';
        }
        if (isset($matches[6])) { // @mago-expect lint:no-isset
            $format .= 's';
        }
        if (isset($matches[7])) { // @mago-expect lint:no-isset
            $format .= '.u';
        }
        if (isset($matches[8])) { // @mago-expect lint:no-isset
            $format .= 'O';
        }

        // @mago-expect analysis:invalid-property-assignment-value
        $this->dateTime = DateTimeImmutable::createFromFormat($format, $value);
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
            return $this->dateTime;
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
