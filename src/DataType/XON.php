<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Extended Composite Name and Identification Number for Organizations
 *
 * @mago-expect lint:excessive-parameter-list
 */
final readonly class XON implements Type
{
    use HasComponents;

    public function __construct(
        public ST $name = new ST(),
        public CWE $nameTypeCode = new CWE(),
        public ST $idNumber = new ST(),
        public ST $identifierCheckDigit = new ST(),
        public ST $checkDigitScheme = new ST(),
        public HD $assigningAuthority = new HD(),
        public ID $identifierTypeCode = new ID(203),
        public HD $assigningFacility = new HD(),
        public ID $nameRepresentationCode = new ID(465),
        public ST $organizationIdentifier = new ST(),
    ) {}
}
