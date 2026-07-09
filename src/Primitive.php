<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

/**
 * Represents HL7 values that have singluar values
 */
interface Primitive extends Type
{
    public function getValue(): string;

    public function setValue(string $value): void;
}
