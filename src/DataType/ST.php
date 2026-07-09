<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use Override;
use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\CanAssertNumbers;
use RoundingWell\HL7\Exception\InvalidValue;

/**
 * String
 */
final class ST extends AbstractPrimitive
{
    use CanAssertNumbers;

    public function __construct(
        private readonly int $minLength = 0,
        private readonly int $maxLength = 0,
    ) {
        $this->assertNaturalNumber($this->minLength);
        $this->assertNaturalNumber($this->maxLength);

        parent::__construct();
    }

    #[Override]
    public function setValue(string $value): void
    {
        if ($this->minLength > 0 && strlen($value) < $this->minLength) {
            throw InvalidValue::minLength('ST', $this->minLength);
        }

        if ($this->maxLength > 0 && strlen($value) > $this->maxLength) {
            throw InvalidValue::maxLength('ST', $this->maxLength);
        }

        parent::setValue($value);
    }
}
