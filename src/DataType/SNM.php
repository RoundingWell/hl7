<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use Override;
use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\Exception\InvalidValue;

/**
 * Sequential Numeric
 */
final class SNM extends AbstractPrimitive
{
    #[Override]
    public function setValue(string $value): void
    {
        if (!ctype_digit($value)) {
            throw InvalidValue::notNumeric('SNM', $value);
        }

        parent::setValue($value);
    }
}
