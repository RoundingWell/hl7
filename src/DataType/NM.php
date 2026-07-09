<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use Override;
use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\Exception\InvalidValue;

/**
 * Numeric
 */
final class NM extends AbstractPrimitive
{
    public function __construct(
        private readonly int $min = PHP_INT_MIN,
        private readonly int $max = PHP_INT_MAX,
    ) {
        parent::__construct();
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    #[Override]
    public function setValue(string $value): void
    {
        if (!is_numeric($value)) {
            throw InvalidValue::notNumeric('NM', $value);
        }

        if ($value < $this->min) {
            throw InvalidValue::minValue('NM', $this->min);
        }

        if ($value > $this->max) {
            throw InvalidValue::maxValue('NM', $this->max);
        }

        parent::setValue($value);
    }
}
