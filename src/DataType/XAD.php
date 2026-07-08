<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Extended Address
 *
 * @mago-expect lint:excessive-parameter-list
 */
final readonly class XAD implements Type
{
    use HasComponents;

    public function __construct(
        public SAD $streetAddress = new SAD(),
        public ST $otherDesignation = new ST(),
        public ST $city = new ST(),
        public ST $stateOrProvince = new ST(),
        public ST $zipOrPostalCode = new ST(),
        public ID $country = new ID(399),
        public ID $addressType = new ID(190),
        public ST $otherGeographicDesignation = new ST(),
        public CWE $countyParishCode = new CWE(),
        public CWE $censusTract = new CWE(),
        public ID $addressRepresentationCode = new ID(465),
        public ST $addressValidityRange = new ST(),
        public DTM $effectiveDate = new DTM(),
        public DTM $expirationDate = new DTM(),
        public CWE $expirationReason = new CWE(),
        public ID $temporaryIndicator = new ID(136),
        public ID $badAddressIndicator = new ID(136),
        public ID $addressUsage = new ID(617),
        public ST $addressee = new ST(),
        public ST $comment = new ST(),
        public NM $preferenceOrder = new NM(),
        public CWE $protectionCode = new CWE(),
        public EI $addressIdentifier = new EI(),
    ) {}
}
