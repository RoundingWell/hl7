<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Extended Composite ID with Check Digit
 *
 * @mago-expect lint:excessive-parameter-list
 */
final readonly class CX implements Type
{
    use HasComponents;

    public function __construct(
        public ST $id = new ST(),
        public ST $identifierCheckDigit = new ST(),
        public ID $checkDigitScheme = new ID(61),
        public HD $assigningAuthority = new HD(),
        public ID $identifierTypeCode = new ID(203),
        public HD $assigningFacility = new HD(),
        public TS $effectiveDate = new TS(),
        public TS $expirationDate = new TS(),
        public CWE $assigningJurisdiction = new CWE(),
        public CWE $assigningAgencyOrDepartment = new CWE(),
        public ST $securityCheck = new ST(),
        public ID $securityCheckScheme = new ID(904),
    ) {}
}
