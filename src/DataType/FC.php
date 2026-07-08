<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Financial Class
 */
final readonly class FC extends Composite
{
    public function __construct(
        public CWE $financialClassCode = new CWE(),
        public DTM $effectiveDate = new DTM(),
    ) {}
}
