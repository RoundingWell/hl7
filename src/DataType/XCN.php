<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Extended Composite ID Number and Name for Persons
 *
 * @mago-expect lint:excessive-parameter-list
 */
final readonly class XCN extends Composite
{
    public function __construct(
        public ST $id = new ST(),
        public FNx $familyName = new FNx(),
        public ST $givenName = new ST(),
        public ST $furtherGivenNames = new ST(),
        public ST $suffix = new ST(),
        public ST $prefix = new ST(),
        public IS $degree = new IS(360),
        public IS $sourceTable = new IS(297),
        public HD $assigningAuthority = new HD(),
        public ID $nameTypeCode = new ID(200),
        public ST $identifierCheckDigit = new ST(),
        public ID $checkDigitScheme = new ID(61),
        public ID $identifierTypeCode = new ID(203),
        public HD $assigningFacility = new HD(),
        public ID $nameRepresentationCode = new ID(465),
        public CE $nameContext = new CE(),
        public DR $nameValidityRange = new DR(),
        public ID $nameAssemblyOrder = new ID(444),
        public TS $effectiveDate = new TS(),
        public TS $expirationDate = new TS(),
        public ST $professionalSuffix = new ST(),
        public CWE $assigningJurisdiction = new CWE(),
        public CWE $assigningAgencyOrDepartment = new CWE(),
    ) {}
}
