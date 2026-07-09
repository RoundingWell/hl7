<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\DataType\CNE;
use RoundingWell\HL7\DataType\CP;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\EI;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\SI;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XCN;
use RoundingWell\HL7\TypeDefinition;

/**
 * Diagnosis Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class DG1 extends AbstractSegment
{
    public function __construct()
    {
        $this->add(new TypeDefinition('Set ID', SI::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Diagnosis Coding Method', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Diagnosis Code', CWE::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Diagnosis Description', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Diagnosis Date/Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Diagnosis Type', CWE::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Major Diagnostic Category', CNE::class, maxReps: 1));
        $this->add(new TypeDefinition('Diagnostic Related Group', CNE::class, maxReps: 1));
        $this->add(new TypeDefinition('DRG Approval Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('DRG Grouper Review Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Outlier Type', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Outlier Days', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Outlier Cost', CP::class, maxReps: 1));
        $this->add(new TypeDefinition('Grouper Version And Type', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Diagnosis Priority', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Diagnosing Clinician', XCN::class));
        $this->add(new TypeDefinition('Diagnosis Classification', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Confidential Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Attestation Date/Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Diagnosis Identifier', EI::class, maxReps: 1));
        $this->add(new TypeDefinition('Diagnosis Action Code', ID::class, args: ['table' => 206], maxReps: 1));
        $this->add(new TypeDefinition('Parent Diagnosis', EI::class, maxReps: 1));
        $this->add(new TypeDefinition('DRG CCL Value Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('DRG Grouping Usage', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('DRG Diagnosis Determination Status', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Present On Admission (POA) Indicator', CWE::class, maxReps: 1));
    }

    public function getIdentity(): SI
    {
        return $this->getFieldRepetition(1, 0);
    }

    public function getCodingMethod(): ST
    {
        return $this->getFieldRepetition(2, 0);
    }

    public function getCode(): CWE
    {
        return $this->getFieldRepetition(3, 0);
    }

    public function getDescription(): ST
    {
        return $this->getFieldRepetition(4, 0);
    }

    public function getDateTime(): DTM
    {
        return $this->getFieldRepetition(5, 0);
    }

    public function getType(): CWE
    {
        return $this->getFieldRepetition(6, 0);
    }

    public function getMajorDiagnosticCategory(): CNE
    {
        return $this->getFieldRepetition(7, 0);
    }

    public function getDiagnosticRelatedGroup(): CNE
    {
        return $this->getFieldRepetition(8, 0);
    }

    public function getDrgApprovalIndicator(): ID
    {
        return $this->getFieldRepetition(9, 0);
    }

    public function getDrgGrouperReviewCode(): CWE
    {
        return $this->getFieldRepetition(10, 0);
    }

    public function getOutlierType(): CWE
    {
        return $this->getFieldRepetition(11, 0);
    }

    public function getOutlierDays(): NM
    {
        return $this->getFieldRepetition(12, 0);
    }

    public function getOutlierCost(): CP
    {
        return $this->getFieldRepetition(13, 0);
    }

    public function getGrouperVersionAndType(): ST
    {
        return $this->getFieldRepetition(14, 0);
    }

    public function getPriority(): NM
    {
        return $this->getFieldRepetition(15, 0);
    }

    /**
     * @return list<XCN>
     */
    public function getDiagnosingClinician(): array
    {
        return $this->getField(16);
    }

    public function getClassification(): CWE
    {
        return $this->getFieldRepetition(17, 0);
    }

    public function getConfidentialIndicator(): ID
    {
        return $this->getFieldRepetition(18, 0);
    }

    public function getAttestationDateTime(): DTM
    {
        return $this->getFieldRepetition(19, 0);
    }

    public function getDiagnosisIdentifier(): EI
    {
        return $this->getFieldRepetition(20, 0);
    }

    public function getActionCode(): ID
    {
        return $this->getFieldRepetition(21, 0);
    }

    public function getParentDiagnosis(): EI
    {
        return $this->getFieldRepetition(22, 0);
    }

    public function getDrgCclValueCode(): CWE
    {
        return $this->getFieldRepetition(23, 0);
    }

    public function getDrgGroupingUsage(): ID
    {
        return $this->getFieldRepetition(24, 0);
    }

    public function getDrgDiagnosisDeterminationStatus(): CWE
    {
        return $this->getFieldRepetition(25, 0);
    }

    public function getPresentOnAdmissionIndicator(): CWE
    {
        return $this->getFieldRepetition(26, 0);
    }
}
