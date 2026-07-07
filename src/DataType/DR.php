<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Date/Time Range
 */
final readonly class DR implements Type
{
    use HasComponents;

    public function __construct(
        public TS $start = new TS(),
        public TS $end = new TS(),
    ) {}
}
