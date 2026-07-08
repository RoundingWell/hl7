<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

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
use RoundingWell\HL7\Field;
use RoundingWell\HL7\Segment;

/**
 * Diagnosis Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class DG1 extends Segment
{
    public function __construct()
    {
        parent::__construct('DG1');

        $this->addField(1, new Field('Set ID', SI::class, required: true));
        $this->addField(2, new Field('Diagnosis Coding Method', ST::class));
        $this->addField(3, new Field('Diagnosis Code', CWE::class, required: true));
        $this->addField(4, new Field('Diagnosis Description', ST::class));
        $this->addField(5, new Field('Diagnosis Date/Time', DTM::class));
        $this->addField(6, new Field('Diagnosis Type', CWE::class, required: true));
        $this->addField(7, new Field('Major Diagnostic Category', CNE::class));
        $this->addField(8, new Field('Diagnostic Related Group', CNE::class));
        $this->addField(9, new Field('DRG Approval Indicator', ID::class, args: ['table' => 136]));
        $this->addField(10, new Field('DRG Grouper Review Code', CWE::class));
        $this->addField(11, new Field('Outlier Type', CWE::class));
        $this->addField(12, new Field('Outlier Days', NM::class));
        $this->addField(13, new Field('Outlier Cost', CP::class));
        $this->addField(14, new Field('Grouper Version And Type', ST::class));
        $this->addField(15, new Field('Diagnosis Priority', NM::class));
        $this->addField(16, new Field('Diagnosing Clinician', XCN::class, repeating: true));
        $this->addField(17, new Field('Diagnosis Classification', CWE::class));
        $this->addField(18, new Field('Confidential Indicator', ID::class, args: ['table' => 136]));
        $this->addField(19, new Field('Attestation Date/Time', DTM::class));
        $this->addField(20, new Field('Diagnosis Identifier', EI::class));
        $this->addField(21, new Field('Diagnosis Action Code', ID::class, args: ['table' => 206]));
        $this->addField(22, new Field('Parent Diagnosis', EI::class));
        $this->addField(23, new Field('DRG CCL Value Code', CWE::class));
        $this->addField(24, new Field('DRG Grouping Usage', ID::class, args: ['table' => 136]));
        $this->addField(25, new Field('DRG Diagnosis Determination Status', CWE::class));
        $this->addField(26, new Field('Present On Admission (POA) Indicator', CWE::class));
    }

    public function getIdentity(): SI
    {
        return $this->getField(1)->getInstance();
    }

    public function getCodingMethod(): ST
    {
        return $this->getField(2)->getInstance();
    }

    public function getCode(): CWE
    {
        return $this->getField(3)->getInstance();
    }

    public function getDescription(): ST
    {
        return $this->getField(4)->getInstance();
    }

    public function getDateTime(): DTM
    {
        return $this->getField(5)->getInstance();
    }

    public function getType(): CWE
    {
        return $this->getField(6)->getInstance();
    }

    public function getMajorDiagnosticCategory(): CNE
    {
        return $this->getField(7)->getInstance();
    }

    public function getDiagnosticRelatedGroup(): CNE
    {
        return $this->getField(8)->getInstance();
    }

    public function getDrgApprovalIndicator(): ID
    {
        return $this->getField(9)->getInstance();
    }

    public function getDrgGrouperReviewCode(): CWE
    {
        return $this->getField(10)->getInstance();
    }

    public function getOutlierType(): CWE
    {
        return $this->getField(11)->getInstance();
    }

    public function getOutlierDays(): NM
    {
        return $this->getField(12)->getInstance();
    }

    public function getOutlierCost(): CP
    {
        return $this->getField(13)->getInstance();
    }

    public function getGrouperVersionAndType(): ST
    {
        return $this->getField(14)->getInstance();
    }

    public function getPriority(): NM
    {
        return $this->getField(15)->getInstance();
    }

    /**
     * @return list<XCN>
     */
    public function getDiagnosingClinician(): array
    {
        return $this->getField(16)->getInstance();
    }

    public function getClassification(): CWE
    {
        return $this->getField(17)->getInstance();
    }

    public function getConfidentialIndicator(): ID
    {
        return $this->getField(18)->getInstance();
    }

    public function getAttestationDateTime(): DTM
    {
        return $this->getField(19)->getInstance();
    }

    public function getDiagnosisIdentifier(): EI
    {
        return $this->getField(20)->getInstance();
    }

    public function getActionCode(): ID
    {
        return $this->getField(21)->getInstance();
    }

    public function getParentDiagnosis(): EI
    {
        return $this->getField(22)->getInstance();
    }

    public function getDrgCclValueCode(): CWE
    {
        return $this->getField(23)->getInstance();
    }

    public function getDrgGroupingUsage(): ID
    {
        return $this->getField(24)->getInstance();
    }

    public function getDrgDiagnosisDeterminationStatus(): CWE
    {
        return $this->getField(25)->getInstance();
    }

    public function getPresentOnAdmissionIndicator(): CWE
    {
        return $this->getField(26)->getInstance();
    }
}
