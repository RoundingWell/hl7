<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use ReflectionProperty;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidValue;

/**
 * Sequential Numeric
 */
final class SNM implements Type, \Stringable
{
    public string $value;

    public function hasValue(): bool
    {
        return new ReflectionProperty($this, 'value')->isInitialized($this);
    }

    public function setValue(string $value): void
    {
        if (!ctype_digit($value)) {
            throw InvalidValue::notNumeric('SNM', $value);
        }

        $this->value = $value;
    }

    #[\Override]
    public function setRaw(Encoding $encoding, string $value, int $depth = 0): void
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
