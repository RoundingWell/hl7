<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use ReflectionProperty;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidValue;

/**
 * Numeric
 */
final class NM implements Type, \Stringable
{
    public string $value;

    public function __construct(
        public readonly int $min = PHP_INT_MIN,
        public readonly int $max = PHP_INT_MAX,
    ) {}

    public function hasValue(): bool
    {
        return new ReflectionProperty($this, 'value')->isInitialized($this);
    }

    public function setValue(string $value): void
    {
        if ($value < $this->min) {
            throw InvalidValue::minValue('NM', $this->min);
        }

        if ($value > $this->max) {
            throw InvalidValue::maxValue('NM', $this->max);
        }

        $this->value = $value;
    }

    #[\Override]
    public function setRaw(Encoding $encoding, string $value): void
    {
        if ($value === '') {
            return;
        }

        $this->setValue($encoding->decode($value));
    }

    public function getValue(): string
    {
        if ($this->hasValue()) {
            return $this->value;
        }

        return '';
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getValue();
    }
}
