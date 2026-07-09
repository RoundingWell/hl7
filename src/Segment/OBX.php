<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\BaseField;
use RoundingWell\HL7\BaseSegment;
use RoundingWell\HL7\DataType\CNE;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\EI;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\SI;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\Varies;
use RoundingWell\HL7\DataType\XAD;
use RoundingWell\HL7\DataType\XCN;
use RoundingWell\HL7\DataType\XON;

/**
 * Observation/Result Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class OBX extends BaseSegment
{
    public function __construct()
    {
        parent::__construct('OBX');

        $this->addField(1, new BaseField('Set ID', SI::class));
        $this->addField(2, new BaseField('Value Type', ID::class, required: true, args: ['table' => 125]));
        $this->addField(3, new BaseField('Observation Identifier', CWE::class, required: true));
        $this->addField(4, new BaseField('Observation Sub-ID', ST::class, required: true));
        $this->addField(5, new BaseField('Observation Value', Varies::class, repeating: true));
        $this->addField(6, new BaseField('Units', CWE::class));
        $this->addField(7, new BaseField('References Range', ST::class));
        $this->addField(8, new BaseField('Interpretation Codes', CWE::class, repeating: true));
        $this->addField(9, new BaseField('Probability', NM::class));
        $this->addField(10, new BaseField('Nature of Abnormal Test', ID::class, repeating: true, args: [
            'table' => 80,
        ]));
        $this->addField(11, new BaseField('Observation Result Status', ID::class, required: true, args: [
            'table' => 85,
        ]));
        $this->addField(12, new BaseField('Effective Date of Reference Range', DTM::class));
        $this->addField(13, new BaseField('User Defined Access Checks', ST::class));
        $this->addField(14, new BaseField('Date/Time of the Observation', DTM::class));
        $this->addField(15, new BaseField("Producer's ID", CWE::class));
        $this->addField(16, new BaseField('Responsible Observer', XCN::class, repeating: true));
        $this->addField(17, new BaseField('Observation Method', CWE::class, repeating: true));
        $this->addField(18, new BaseField('Equipment Instance Identifier', EI::class, repeating: true));
        $this->addField(19, new BaseField('Date/Time of the Analysis', DTM::class));
        $this->addField(20, new BaseField('Observation Site', CWE::class, repeating: true));
        $this->addField(21, new BaseField('Observation Instance Identifier', EI::class));
        $this->addField(22, new BaseField('Mood Code', CNE::class));
        $this->addField(23, new BaseField('Performing Organization Name', XON::class));
        $this->addField(24, new BaseField('Performing Organization Address', XAD::class));
        $this->addField(25, new BaseField('Performing Organization Medical Director', XCN::class));
        $this->addField(26, new BaseField('Patient Results Release Category', ID::class, args: ['table' => 909]));
        $this->addField(27, new BaseField('Root Cause', CWE::class));
        $this->addField(28, new BaseField('Local Process Control', CWE::class, repeating: true));
    }

    public function getIdentity(): SI
    {
        return $this->getField(1)->getInstance();
    }

    public function getValueType(): ID
    {
        return $this->getField(2)->getInstance();
    }

    public function getObservationIdentifier(): CWE
    {
        return $this->getField(3)->getInstance();
    }

    public function getObservationSubId(): ST
    {
        return $this->getField(4)->getInstance();
    }

    /**
     * @return list<Varies>
     */
    public function getObservationValue(): array
    {
        return $this->getField(5)->getInstance();
    }

    public function getUnits(): CWE
    {
        return $this->getField(6)->getInstance();
    }

    public function getReferencesRange(): ST
    {
        return $this->getField(7)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getInterpretationCodes(): array
    {
        return $this->getField(8)->getInstance();
    }

    public function getProbability(): NM
    {
        return $this->getField(9)->getInstance();
    }

    /**
     * @return list<ID>
     */
    public function getNatureOfAbnormalTest(): array
    {
        return $this->getField(10)->getInstance();
    }

    public function getObservationResultStatus(): ID
    {
        return $this->getField(11)->getInstance();
    }

    public function getEffectiveDateOfReferenceRange(): DTM
    {
        return $this->getField(12)->getInstance();
    }

    public function getUserDefinedAccessChecks(): ST
    {
        return $this->getField(13)->getInstance();
    }

    public function getObservationDateTime(): DTM
    {
        return $this->getField(14)->getInstance();
    }

    public function getProducerId(): CWE
    {
        return $this->getField(15)->getInstance();
    }

    /**
     * @return list<XCN>
     */
    public function getResponsibleObserver(): array
    {
        return $this->getField(16)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getObservationMethod(): array
    {
        return $this->getField(17)->getInstance();
    }

    /**
     * @return list<EI>
     */
    public function getEquipmentInstanceIdentifier(): array
    {
        return $this->getField(18)->getInstance();
    }

    public function getAnalysisDateTime(): DTM
    {
        return $this->getField(19)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getObservationSite(): array
    {
        return $this->getField(20)->getInstance();
    }

    public function getObservationInstanceIdentifier(): EI
    {
        return $this->getField(21)->getInstance();
    }

    public function getMoodCode(): CNE
    {
        return $this->getField(22)->getInstance();
    }

    public function getPerformingOrganizationName(): XON
    {
        return $this->getField(23)->getInstance();
    }

    public function getPerformingOrganizationAddress(): XAD
    {
        return $this->getField(24)->getInstance();
    }

    public function getPerformingOrganizationMedicalDirector(): XCN
    {
        return $this->getField(25)->getInstance();
    }

    public function getPatientResultsReleaseCategory(): ID
    {
        return $this->getField(26)->getInstance();
    }

    public function getRootCause(): CWE
    {
        return $this->getField(27)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getLocalProcessControl(): array
    {
        return $this->getField(28)->getInstance();
    }
}
