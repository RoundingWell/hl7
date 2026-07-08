<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Financial Class
 */
final readonly class FC implements Type
{
    use HasComponents;

    public function __construct(
        public CWE $financialClassCode = new CWE(),
        public DTM $effectiveDate = new DTM(),
    ) {}
}
