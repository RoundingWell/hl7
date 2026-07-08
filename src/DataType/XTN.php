<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Extended Telecommunication Number
 *
 * @mago-expect lint:excessive-parameter-list
 */
final readonly class XTN extends Composite
{
    public function __construct(
        public ST $telephoneNumber = new ST(),
        public ST $telecommunicationUseCode = new ST(),
        public ST $telecommunicationEquipmentType = new ST(),
        public ST $communicationAddress = new ST(),
        public SNM $countryCode = new SNM(),
        public SNM $areaCityCode = new SNM(),
        public SNM $localNumber = new SNM(),
        public SNM $extension = new SNM(),
        public ST $anyText = new ST(),
        public ST $extensionPrefix = new ST(),
        public ST $speedDialCode = new ST(),
        public ST $unformattedTelephoneNumber = new ST(),
        public DTM $effectiveStartDate = new DTM(),
        public DTM $expirationDate = new DTM(),
        public CWE $expirationReason = new CWE(),
        public CWE $protectionCode = new CWE(),
        public EI $sharedTelecommunicationIdentifier = new EI(),
        public NM $preferenceOrder = new NM(),
    ) {}
}
