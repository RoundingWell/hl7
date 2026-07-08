<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Entity Identifier
 */
final readonly class EI extends Composite
{
    public function __construct(
        public ST $id = new ST(),
        public IS $namespaceId = new IS(363),
        public ST $universalId = new ST(),
        public ID $universalIdType = new ID(301),
    ) {}
}
