<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\DataType\CNE;
use RoundingWell\HL7\DataType\CP;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\MO;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XPN;
use RoundingWell\HL7\TypeDefinition;

/**
 * Diagnosis Related Group Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class DRG extends AbstractSegment
{
    public function __construct()
    {
        $this->add(new TypeDefinition('Diagnostic Related Group', CNE::class, maxReps: 1));
        $this->add(new TypeDefinition('DRG Assigned Date/Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('DRG Approval Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('DRG Grouper Review Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Outlier Type', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Outlier Days', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Outlier Cost', CP::class, maxReps: 1));
        $this->add(new TypeDefinition('DRG Payor', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Outlier Reimbursement', CP::class, maxReps: 1));
        $this->add(new TypeDefinition('Confidential Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('DRG Transfer Type', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Name of Coder', XPN::class, maxReps: 1));
        $this->add(new TypeDefinition('Grouper Status', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('PCCL Value Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Effective Weight', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Monetary Amount', MO::class, maxReps: 1));
        $this->add(new TypeDefinition('Status Patient', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Grouper Software Name', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Grouper Software Version', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Status Financial Calculation', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Relative Discount/Surcharge', MO::class, maxReps: 1));
        $this->add(new TypeDefinition('Basic Charge', MO::class, maxReps: 1));
        $this->add(new TypeDefinition('Total Charge', MO::class, maxReps: 1));
        $this->add(new TypeDefinition('Discount/Surcharge', MO::class, maxReps: 1));
        $this->add(new TypeDefinition('Calculated Days', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Status Gender', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Status Age', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Status Length of Stay', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Status Same Day Flag', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Status Separation Mode', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Status Weight at Birth', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Status Respiration Minutes', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Status Admission', CWE::class, maxReps: 1));
    }

    public function getDiagnosticRelatedGroup(): CNE
    {
        return $this->getFieldRepetition(1, 0);
    }

    public function getAssignedDateTime(): DTM
    {
        return $this->getFieldRepetition(2, 0);
    }

    public function getApprovalIndicator(): ID
    {
        return $this->getFieldRepetition(3, 0);
    }

    public function getGrouperReviewCode(): CWE
    {
        return $this->getFieldRepetition(4, 0);
    }

    public function getOutlierType(): CWE
    {
        return $this->getFieldRepetition(5, 0);
    }

    public function getOutlierDays(): NM
    {
        return $this->getFieldRepetition(6, 0);
    }

    public function getOutlierCost(): CP
    {
        return $this->getFieldRepetition(7, 0);
    }

    public function getPayor(): CWE
    {
        return $this->getFieldRepetition(8, 0);
    }

    public function getOutlierReimbursement(): CP
    {
        return $this->getFieldRepetition(9, 0);
    }

    public function getConfidentialIndicator(): ID
    {
        return $this->getFieldRepetition(10, 0);
    }

    public function getTransferType(): CWE
    {
        return $this->getFieldRepetition(11, 0);
    }

    public function getNameOfCoder(): XPN
    {
        return $this->getFieldRepetition(12, 0);
    }

    public function getGrouperStatus(): CWE
    {
        return $this->getFieldRepetition(13, 0);
    }

    public function getPcclValueCode(): CWE
    {
        return $this->getFieldRepetition(14, 0);
    }

    public function getEffectiveWeight(): NM
    {
        return $this->getFieldRepetition(15, 0);
    }

    public function getMonetaryAmount(): MO
    {
        return $this->getFieldRepetition(16, 0);
    }

    public function getStatusPatient(): CWE
    {
        return $this->getFieldRepetition(17, 0);
    }

    public function getGrouperSoftwareName(): ST
    {
        return $this->getFieldRepetition(18, 0);
    }

    public function getGrouperSoftwareVersion(): ST
    {
        return $this->getFieldRepetition(19, 0);
    }

    public function getStatusFinancialCalculation(): CWE
    {
        return $this->getFieldRepetition(20, 0);
    }

    public function getRelativeDiscountSurcharge(): MO
    {
        return $this->getFieldRepetition(21, 0);
    }

    public function getBasicCharge(): MO
    {
        return $this->getFieldRepetition(22, 0);
    }

    public function getTotalCharge(): MO
    {
        return $this->getFieldRepetition(23, 0);
    }

    public function getDiscountSurcharge(): MO
    {
        return $this->getFieldRepetition(24, 0);
    }

    public function getCalculatedDays(): NM
    {
        return $this->getFieldRepetition(25, 0);
    }

    public function getStatusGender(): CWE
    {
        return $this->getFieldRepetition(26, 0);
    }

    public function getStatusAge(): CWE
    {
        return $this->getFieldRepetition(27, 0);
    }

    public function getStatusLengthOfStay(): CWE
    {
        return $this->getFieldRepetition(28, 0);
    }

    public function getStatusSameDayFlag(): CWE
    {
        return $this->getFieldRepetition(29, 0);
    }

    public function getStatusSeparationMode(): CWE
    {
        return $this->getFieldRepetition(30, 0);
    }

    public function getStatusWeightAtBirth(): CWE
    {
        return $this->getFieldRepetition(31, 0);
    }

    public function getStatusRespirationMinutes(): CWE
    {
        return $this->getFieldRepetition(32, 0);
    }

    public function getStatusAdmission(): CWE
    {
        return $this->getFieldRepetition(33, 0);
    }
}
