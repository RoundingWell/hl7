<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use ReflectionProperty;
use RoundingWell\HL7\Encoding;

/**
 * Identifier
 */
final class ID implements Type, \Stringable
{
    public string $value;

    public function __construct(
        public readonly int $table,
    ) {}

    public function hasValue(): bool
    {
        // @mago-expect analysis:unhandled-thrown-type
        return new ReflectionProperty($this, 'value')->isInitialized($this);
    }

    public function setValue(string $value): void
    {
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
