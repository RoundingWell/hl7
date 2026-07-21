<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

/**
 * A named HL7 field
 *
 * @see Composite
 * @see Primitive
 */
interface Field
{
    public function setField(string $name): void;

    public function getField(): string;
}
