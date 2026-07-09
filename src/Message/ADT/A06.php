<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Message\ADT;

use RoundingWell\HL7\Message;
use RoundingWell\HL7\Segment\DG1;
use RoundingWell\HL7\Segment\DRG;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\NK1;
use RoundingWell\HL7\Segment\OBX;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;
use RoundingWell\HL7\Segment\PV2;

/**
 * A06: Change an Outpatient to an Inpatient
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
 * 9. MRG (Merge Patient Information) (optional)
 * 10. NK1 (Next of Kin / Associated Parties) (optional repeating)
 * 11. PV1 (Patient Visit)
 * 12. PV2 (Patient Visit - Additional Information) (optional)
 * 13. ARV (Access Restriction) (optional repeating)
 * 14. ROL (Role) (optional repeating)
 * 15. DB1 (Disability) (optional repeating)
 * 16. OBX (Observation/Result) (optional repeating)
 * 17. AL1 (Patient Allergy Information) (optional repeating)
 * 18. DG1 (Diagnosis) (optional repeating)
 * 19. DRG (Diagnosis Related Group) (optional)
 * 20. ADT_A06_PROCEDURE (a Group object) (optional repeating)
 * 21. GT1 (Guarantor) (optional repeating)
 * 22. ADT_A06_INSURANCE (a Group object) (optional repeating)
 * 23. ACC (Accident) (optional)
 * 24. UB1 (Uniform Billing) (optional)
 * 25. UB2 (Uniform Billing Data) (optional)
 */
final readonly class A06 extends Message
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
