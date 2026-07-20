<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Message\ADT;

use RoundingWell\HL7\AbstractMessage;
use RoundingWell\HL7\GenericSegment;
use RoundingWell\HL7\Segment\DG1;
use RoundingWell\HL7\Segment\DRG;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\Segment\NK1;
use RoundingWell\HL7\Segment\OBX;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;
use RoundingWell\HL7\Segment\PV2;
use RoundingWell\HL7\StructureDefinition;

/**
 * A03: Discharge / End Visit
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
 * 12. ARV2 (Access Restriction, 2nd position) (optional repeating)
 * 13. ROL2 (Role, 2nd position) (optional repeating)
 * 14. DB1 (Disability) (optional repeating)
 * 15. AL1 (Patient Allergy Information) (optional repeating)
 * 16. DG1 (Diagnosis) (optional repeating)
 * 17. DRG (Diagnosis Related Group) (optional)
 * 18. ADT_A03_PROCEDURE (a Group object) (optional repeating)
 * 19. OBX (Observation/Result) (optional repeating)
 * 20. GT1 (Guarantor) (optional repeating)
 * 21. ADT_A03_INSURANCE (a Group object) (optional repeating)
 * 22. ACC (Accident) (optional)
 * 23. PDA (Patient Death and Autopsy) (optional)
 */
class A03 extends AbstractMessage
{
    public function __construct()
    {
        $this->add('MSH', new StructureDefinition(MSH::class, isRequired: true));
        $this->add('SFT', new StructureDefinition(GenericSegment::class, ['SFT'], isRepeating: true));
        $this->add('UAC', new StructureDefinition(GenericSegment::class, ['UAC']));
        $this->add('EVN', new StructureDefinition(EVN::class, isRequired: true));
        $this->add('PID', new StructureDefinition(PID::class, isRequired: true));
        $this->add('PD1', new StructureDefinition(GenericSegment::class, ['PD1']));
        $this->add('ARV', new StructureDefinition(GenericSegment::class, ['ARV'], isRepeating: true));
        $this->add('ROL', new StructureDefinition(GenericSegment::class, ['ROL'], isRepeating: true));
        $this->add('NK1', new StructureDefinition(NK1::class, isRepeating: true));
        $this->add('PV1', new StructureDefinition(PV1::class, isRequired: true));
        $this->add('PV2', new StructureDefinition(PV2::class));
        $this->add('ARV2', new StructureDefinition(GenericSegment::class, ['ARV'], isRepeating: true));
        $this->add('ROL2', new StructureDefinition(GenericSegment::class, ['ROL'], isRepeating: true));
        $this->add('DB1', new StructureDefinition(GenericSegment::class, ['DB1'], isRepeating: true));
        $this->add('AL1', new StructureDefinition(GenericSegment::class, ['AL1'], isRepeating: true));
        $this->add('DG1', new StructureDefinition(DG1::class, isRepeating: true));
        $this->add('DRG', new StructureDefinition(DRG::class));
        $this->add('PROCEDURE', new StructureDefinition(A03Procedure::class, isRepeating: true));
        $this->add('OBX', new StructureDefinition(OBX::class, isRepeating: true));
        $this->add('GT1', new StructureDefinition(GenericSegment::class, ['GT1'], isRepeating: true));
        $this->add('INSURANCE', new StructureDefinition(A03Insurance::class, isRepeating: true));
        $this->add('ACC', new StructureDefinition(GenericSegment::class, ['ACC']));
        $this->add('PDA', new StructureDefinition(GenericSegment::class, ['PDA']));
    }

    public function getEVN(): EVN
    {
        return $this->get('EVN');
    }

    public function getPID(): PID
    {
        return $this->get('PID');
    }

    public function getPV1(): PV1
    {
        return $this->get('PV1');
    }

    public function getPV2(): ?PV2
    {
        return $this->getAll('PV2')[0] ?? null;
    }

    public function getDRG(): ?DRG
    {
        return $this->getAll('DRG')[0] ?? null;
    }

    /**
     * @return list<NK1>
     */
    public function listNK1(): array
    {
        return $this->getAll('NK1');
    }

    /**
     * @return list<OBX>
     */
    public function listOBX(): array
    {
        return $this->getAll('OBX');
    }

    /**
     * @return list<DG1>
     */
    public function listDG1(): array
    {
        return $this->getAll('DG1');
    }
}
