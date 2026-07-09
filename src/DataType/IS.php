<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\CanAssertNumbers;

/**
 * User-Supplied Identifier
 *
 * Effectively the same as {@see ID}, but the value comes from a user-supplied table.
 */
final class IS extends AbstractPrimitive
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
