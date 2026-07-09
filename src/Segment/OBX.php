<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\DataType\CNE;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\EI;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\SI;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XAD;
use RoundingWell\HL7\DataType\XCN;
use RoundingWell\HL7\DataType\XON;
use RoundingWell\HL7\TypeDefinition;
use RoundingWell\HL7\Varies;

/**
 * Observation/Result Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class OBX extends AbstractSegment
{
    public function __construct()
    {
        $this->add(new TypeDefinition('Set ID', SI::class, maxReps: 1));
        $this->add(new TypeDefinition('Value Type', ID::class, args: ['table' => 125], isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Observation Identifier', CWE::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Observation Sub-ID', ST::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Observation Value', Varies::class));
        $this->add(new TypeDefinition('Units', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('References Range', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Interpretation Codes', CWE::class));
        $this->add(new TypeDefinition('Probability', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Nature of Abnormal Test', ID::class, args: ['table' => 80]));
        $this->add(
            new TypeDefinition(
                'Observation Result Status',
                ID::class,
                args: ['table' => 85],
                isRequired: true,
                maxReps: 1,
            ),
        );
        $this->add(new TypeDefinition('Effective Date of Reference Range', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('User Defined Access Checks', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Date/Time of the Observation', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition("Producer's ID", CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Responsible Observer', XCN::class));
        $this->add(new TypeDefinition('Observation Method', CWE::class));
        $this->add(new TypeDefinition('Equipment Instance Identifier', EI::class));
        $this->add(new TypeDefinition('Date/Time of the Analysis', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Observation Site', CWE::class));
        $this->add(new TypeDefinition('Observation Instance Identifier', EI::class, maxReps: 1));
        $this->add(new TypeDefinition('Mood Code', CNE::class, maxReps: 1));
        $this->add(new TypeDefinition('Performing Organization Name', XON::class, maxReps: 1));
        $this->add(new TypeDefinition('Performing Organization Address', XAD::class, maxReps: 1));
        $this->add(new TypeDefinition('Performing Organization Medical Director', XCN::class, maxReps: 1));
        $this->add(
            new TypeDefinition('Patient Results Release Category', ID::class, args: ['table' => 909], maxReps: 1),
        );
        $this->add(new TypeDefinition('Root Cause', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Local Process Control', CWE::class));
    }

    public function getIdentity(): SI
    {
        return $this->getFieldRepetition(1, 0);
    }

    public function getValueType(): ID
    {
        return $this->getFieldRepetition(2, 0);
    }

    public function getObservationIdentifier(): CWE
    {
        return $this->getFieldRepetition(3, 0);
    }

    public function getObservationSubId(): ST
    {
        return $this->getFieldRepetition(4, 0);
    }

    /**
     * @return list<Varies>
     */
    public function getObservationValue(): array
    {
        return $this->getField(5);
    }

    public function getUnits(): CWE
    {
        return $this->getFieldRepetition(6, 0);
    }

    public function getReferencesRange(): ST
    {
        return $this->getFieldRepetition(7, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getInterpretationCodes(): array
    {
        return $this->getField(8);
    }

    public function getProbability(): NM
    {
        return $this->getFieldRepetition(9, 0);
    }

    /**
     * @return list<ID>
     */
    public function getNatureOfAbnormalTest(): array
    {
        return $this->getField(10);
    }

    public function getObservationResultStatus(): ID
    {
        return $this->getFieldRepetition(11, 0);
    }

    public function getEffectiveDateOfReferenceRange(): DTM
    {
        return $this->getFieldRepetition(12, 0);
    }

    public function getUserDefinedAccessChecks(): ST
    {
        return $this->getFieldRepetition(13, 0);
    }

    public function getObservationDateTime(): DTM
    {
        return $this->getFieldRepetition(14, 0);
    }

    public function getProducerId(): CWE
    {
        return $this->getFieldRepetition(15, 0);
    }

    /**
     * @return list<XCN>
     */
    public function getResponsibleObserver(): array
    {
        return $this->getField(16);
    }

    /**
     * @return list<CWE>
     */
    public function getObservationMethod(): array
    {
        return $this->getField(17);
    }

    /**
     * @return list<EI>
     */
    public function getEquipmentInstanceIdentifier(): array
    {
        return $this->getField(18);
    }

    public function getAnalysisDateTime(): DTM
    {
        return $this->getFieldRepetition(19, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getObservationSite(): array
    {
        return $this->getField(20);
    }

    public function getObservationInstanceIdentifier(): EI
    {
        return $this->getFieldRepetition(21, 0);
    }

    public function getMoodCode(): CNE
    {
        return $this->getFieldRepetition(22, 0);
    }

    public function getPerformingOrganizationName(): XON
    {
        return $this->getFieldRepetition(23, 0);
    }

    public function getPerformingOrganizationAddress(): XAD
    {
        return $this->getFieldRepetition(24, 0);
    }

    public function getPerformingOrganizationMedicalDirector(): XCN
    {
        return $this->getFieldRepetition(25, 0);
    }

    public function getPatientResultsReleaseCategory(): ID
    {
        return $this->getFieldRepetition(26, 0);
    }

    public function getRootCause(): CWE
    {
        return $this->getFieldRepetition(27, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getLocalProcessControl(): array
    {
        return $this->getField(28);
    }
}
