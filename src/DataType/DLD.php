<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Discharge to Location and Date
 */
final readonly class DLD extends Composite
{
    public function __construct(
        public CWE $dischargeToLocation = new CWE(),
        public DTM $effectiveDate = new DTM(),
    ) {}
}
