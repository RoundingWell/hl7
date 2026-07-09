<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\CanAssertNumbers;

/**
 * Identifier
 */
final class ID extends AbstractPrimitive
{
    use CanAssertNumbers;

    public function __construct(
        private readonly int $table,
    ) {
        $this->assertNaturalNumber($this->table);

        parent::__construct();
    }

    public function getTable(): int
    {
        return $this->table;
    }
}
