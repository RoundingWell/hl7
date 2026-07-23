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

    /**
     * DRG.1 Diagnostic Related Group
     */
    public function getDiagnosticRelatedGroup(): CNE
    {
        return $this->getFieldRepetition(1, 0);
    }

    /**
     * DRG.2 DRG Assigned Date/Time
     */
    public function getAssignedDateTime(): DTM
    {
        return $this->getFieldRepetition(2, 0);
    }

    /**
     * DRG.3 DRG Approval Indicator
     */
    public function getApprovalIndicator(): ID
    {
        return $this->getFieldRepetition(3, 0);
    }

    /**
     * DRG.4 DRG Grouper Review Code
     */
    public function getGrouperReviewCode(): CWE
    {
        return $this->getFieldRepetition(4, 0);
    }

    /**
     * DRG.5 Outlier Type
     */
    public function getOutlierType(): CWE
    {
        return $this->getFieldRepetition(5, 0);
    }

    /**
     * DRG.6 Outlier Days
     */
    public function getOutlierDays(): NM
    {
        return $this->getFieldRepetition(6, 0);
    }

    /**
     * DRG.7 Outlier Cost
     */
    public function getOutlierCost(): CP
    {
        return $this->getFieldRepetition(7, 0);
    }

    /**
     * DRG.8 DRG Payor
     */
    public function getPayor(): CWE
    {
        return $this->getFieldRepetition(8, 0);
    }

    /**
     * DRG.9 Outlier Reimbursement
     */
    public function getOutlierReimbursement(): CP
    {
        return $this->getFieldRepetition(9, 0);
    }

    /**
     * DRG.10 Confidential Indicator
     */
    public function getConfidentialIndicator(): ID
    {
        return $this->getFieldRepetition(10, 0);
    }

    /**
     * DRG.11 DRG Transfer Type
     */
    public function getTransferType(): CWE
    {
        return $this->getFieldRepetition(11, 0);
    }

    /**
     * DRG.12 Name of Coder
     */
    public function getNameOfCoder(): XPN
    {
        return $this->getFieldRepetition(12, 0);
    }

    /**
     * DRG.13 Grouper Status
     */
    public function getGrouperStatus(): CWE
    {
        return $this->getFieldRepetition(13, 0);
    }

    /**
     * DRG.14 PCCL Value Code
     */
    public function getPcclValueCode(): CWE
    {
        return $this->getFieldRepetition(14, 0);
    }

    /**
     * DRG.15 Effective Weight
     */
    public function getEffectiveWeight(): NM
    {
        return $this->getFieldRepetition(15, 0);
    }

    /**
     * DRG.16 Monetary Amount
     */
    public function getMonetaryAmount(): MO
    {
        return $this->getFieldRepetition(16, 0);
    }

    /**
     * DRG.17 Status Patient
     */
    public function getStatusPatient(): CWE
    {
        return $this->getFieldRepetition(17, 0);
    }

    /**
     * DRG.18 Grouper Software Name
     */
    public function getGrouperSoftwareName(): ST
    {
        return $this->getFieldRepetition(18, 0);
    }

    /**
     * DRG.19 Grouper Software Version
     */
    public function getGrouperSoftwareVersion(): ST
    {
        return $this->getFieldRepetition(19, 0);
    }

    /**
     * DRG.20 Status Financial Calculation
     */
    public function getStatusFinancialCalculation(): CWE
    {
        return $this->getFieldRepetition(20, 0);
    }

    /**
     * DRG.21 Relative Discount/Surcharge
     */
    public function getRelativeDiscountSurcharge(): MO
    {
        return $this->getFieldRepetition(21, 0);
    }

    /**
     * DRG.22 Basic Charge
     */
    public function getBasicCharge(): MO
    {
        return $this->getFieldRepetition(22, 0);
    }

    /**
     * DRG.23 Total Charge
     */
    public function getTotalCharge(): MO
    {
        return $this->getFieldRepetition(23, 0);
    }

    /**
     * DRG.24 Discount/Surcharge
     */
    public function getDiscountSurcharge(): MO
    {
        return $this->getFieldRepetition(24, 0);
    }

    /**
     * DRG.25 Calculated Days
     */
    public function getCalculatedDays(): NM
    {
        return $this->getFieldRepetition(25, 0);
    }

    /**
     * DRG.26 Status Gender
     */
    public function getStatusGender(): CWE
    {
        return $this->getFieldRepetition(26, 0);
    }

    /**
     * DRG.27 Status Age
     */
    public function getStatusAge(): CWE
    {
        return $this->getFieldRepetition(27, 0);
    }

    /**
     * DRG.28 Status Length of Stay
     */
    public function getStatusLengthOfStay(): CWE
    {
        return $this->getFieldRepetition(28, 0);
    }

    /**
     * DRG.29 Status Same Day Flag
     */
    public function getStatusSameDayFlag(): CWE
    {
        return $this->getFieldRepetition(29, 0);
    }

    /**
     * DRG.30 Status Separation Mode
     */
    public function getStatusSeparationMode(): CWE
    {
        return $this->getFieldRepetition(30, 0);
    }

    /**
     * DRG.31 Status Weight at Birth
     */
    public function getStatusWeightAtBirth(): CWE
    {
        return $this->getFieldRepetition(31, 0);
    }

    /**
     * DRG.32 Status Respiration Minutes
     */
    public function getStatusRespirationMinutes(): CWE
    {
        return $this->getFieldRepetition(32, 0);
    }

    /**
     * DRG.33 Status Admission
     */
    public function getStatusAdmission(): CWE
    {
        return $this->getFieldRepetition(33, 0);
    }
}
