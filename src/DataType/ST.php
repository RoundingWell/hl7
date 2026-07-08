<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use ReflectionProperty;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidValue;

/**
 * String
 */
final class ST implements Type, \Stringable
{
    public string $value;

    public function __construct(
        public readonly int $minLength = 1,
        public readonly int $maxLength = 0,
    ) {}

    public function hasValue(): bool
    {
        // @mago-expect analysis:unhandled-thrown-type
        return new ReflectionProperty($this, 'value')->isInitialized($this);
    }

    public function setValue(string $value): void
    {
        if (strlen($value) < $this->minLength) {
            throw InvalidValue::minLength('ST', $this->minLength);
        }

        if ($this->maxLength > 0 && strlen($value) > $this->maxLength) {
            throw InvalidValue::maxLength('ST', $this->maxLength);
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
