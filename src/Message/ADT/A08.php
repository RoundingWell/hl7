<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Message\ADT;

use RoundingWell\HL7\BaseMessage;
use RoundingWell\HL7\Segment\DG1;
use RoundingWell\HL7\Segment\DRG;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\NK1;
use RoundingWell\HL7\Segment\OBX;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;
use RoundingWell\HL7\Segment\PV2;

/**
 * A08: Update Patient Information
 *
 * A08 reuses the HAPI ADT_A01 message structure.
 *
 * Segments:
 *
 * 1. MSH (Message Header)
 * 2. SFT (Software Segment) (optional repeating)
 * 3. UAC (User Authentication Credential Segment) (optional)
 * 4. EVN (Event Type)
 * 5. PID (Patient Identification)
 * 6. PD1 (Patient Additional Demographic) (optional)
 * 7. ARV (Access Restriction) (optional repeating)
 * 8. ROL (Role) (optional repeating)
 * 9. NK1 (Next of Kin / Associated Parties) (optional repeating)
 * 10. PV1 (Patient Visit)
 * 11. PV2 (Patient Visit - Additional Information) (optional)
 * 12. ARV (Access Restriction) (optional repeating)
 * 13. ROL (Role) (optional repeating)
 * 14. DB1 (Disability) (optional repeating)
 * 15. OBX (Observation/Result) (optional repeating)
 * 16. AL1 (Patient Allergy Information) (optional repeating)
 * 17. DG1 (Diagnosis) (optional repeating)
 * 18. DRG (Diagnosis Related Group) (optional)
 * 19. ADT_A01_PROCEDURE (a Group object) (optional repeating)
 * 20. GT1 (Guarantor) (optional repeating)
 * 21. ADT_A01_INSURANCE (a Group object) (optional repeating)
 * 22. ACC (Accident) (optional)
 * 23. UB1 (Uniform Billing) (optional)
 * 24. UB2 (Uniform Billing Data) (optional)
 * 25. PDA (Patient Death and Autopsy) (optional)
 */
final readonly class A08 extends BaseMessage
{
    public function getEVN(): EVN
    {
        return $this->getRequiredSegment('EVN');
    }

    public function getPID(): PID
    {
        return $this->getRequiredSegment('PID');
    }

    public function getPV1(): PV1
    {
        return $this->getRequiredSegment('PV1');
    }

    /**
     * @return list<NK1>
     */
    public function listNK1(): array
    {
        return $this->getAllSegments('NK1');
    }

    public function getPV2(): ?PV2
    {
        return $this->getSegment('PV2');
    }

    /**
     * @return list<OBX>
     */
    public function listOBX(): array
    {
        return $this->getAllSegments('OBX');
    }

    /**
     * @return list<DG1>
     */
    public function listDG1(): array
    {
        return $this->getAllSegments('DG1');
    }

    public function getDRG(): ?DRG
    {
        return $this->getSegment('DRG');
    }
}
