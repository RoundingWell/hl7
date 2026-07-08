<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Hierarchic Designator
 */
final readonly class HD extends Composite
{
    public function __construct(
        public IS $namespaceId = new IS(300),
        public ST $universalId = new ST(),
        public ID $universalIdType = new ID(301),
    ) {}
}
