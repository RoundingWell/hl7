<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Street Address
 */
final readonly class SAD implements Type
{
    use HasComponents;

    public function __construct(
        public ST $streetAddress = new ST(),
        public ST $streetName = new ST(),
        public ST $dwellingNumber = new ST(),
    ) {}
}
