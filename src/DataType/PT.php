<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Processing Type
 */
final readonly class PT implements Type
{
    use HasComponents;

    public function __construct(
        public ID $id = new ID(103),
        public ID $mode = new ID(207),
    ) {}
}
