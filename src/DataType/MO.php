<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Money
 */
final readonly class MO extends Composite
{
    public function __construct(
        public NM $quantity = new NM(),
        public ID $denomination = new ID(913),
    ) {}
}
