<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Timestamp
 */
final readonly class TS implements Type
{
    use HasComponents;

    public function __construct(
        public DTM $time = new DTM(),
        public ID $precision = new ID(529),
    ) {}
}
