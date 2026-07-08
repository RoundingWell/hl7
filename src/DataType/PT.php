<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Processing Type
 */
final readonly class PT extends Composite
{
    public function __construct(
        public ID $id = new ID(103),
        public ID $mode = new ID(207),
    ) {}
}
