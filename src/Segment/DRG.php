<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\BaseField;
use RoundingWell\HL7\BaseSegment;
use RoundingWell\HL7\DataType\CNE;
use RoundingWell\HL7\DataType\CP;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\MO;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XPN;

/**
 * Diagnosis Related Group Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class DRG extends BaseSegment
{
    public function __construct()
    {
        parent::__construct('DRG');

        $this->addField(1, new BaseField('Diagnostic Related Group', CNE::class));
        $this->addField(2, new BaseField('DRG Assigned Date/Time', DTM::class));
        $this->addField(3, new BaseField('DRG Approval Indicator', ID::class, args: ['table' => 136]));
        $this->addField(4, new BaseField('DRG Grouper Review Code', CWE::class));
        $this->addField(5, new BaseField('Outlier Type', CWE::class));
        $this->addField(6, new BaseField('Outlier Days', NM::class));
        $this->addField(7, new BaseField('Outlier Cost', CP::class));
        $this->addField(8, new BaseField('DRG Payor', CWE::class));
        $this->addField(9, new BaseField('Outlier Reimbursement', CP::class));
        $this->addField(10, new BaseField('Confidential Indicator', ID::class, args: ['table' => 136]));
        $this->addField(11, new BaseField('DRG Transfer Type', CWE::class));
        $this->addField(12, new BaseField('Name of Coder', XPN::class));
        $this->addField(13, new BaseField('Grouper Status', CWE::class));
        $this->addField(14, new BaseField('PCCL Value Code', CWE::class));
        $this->addField(15, new BaseField('Effective Weight', NM::class));
        $this->addField(16, new BaseField('Monetary Amount', MO::class));
        $this->addField(17, new BaseField('Status Patient', CWE::class));
        $this->addField(18, new BaseField('Grouper Software Name', ST::class));
        $this->addField(19, new BaseField('Grouper Software Version', ST::class));
        $this->addField(20, new BaseField('Status Financial Calculation', CWE::class));
        $this->addField(21, new BaseField('Relative Discount/Surcharge', MO::class));
        $this->addField(22, new BaseField('Basic Charge', MO::class));
        $this->addField(23, new BaseField('Total Charge', MO::class));
        $this->addField(24, new BaseField('Discount/Surcharge', MO::class));
        $this->addField(25, new BaseField('Calculated Days', NM::class));
        $this->addField(26, new BaseField('Status Gender', CWE::class));
        $this->addField(27, new BaseField('Status Age', CWE::class));
        $this->addField(28, new BaseField('Status Length of Stay', CWE::class));
        $this->addField(29, new BaseField('Status Same Day Flag', CWE::class));
        $this->addField(30, new BaseField('Status Separation Mode', CWE::class));
        $this->addField(31, new BaseField('Status Weight at Birth', CWE::class));
        $this->addField(32, new BaseField('Status Respiration Minutes', CWE::class));
        $this->addField(33, new BaseField('Status Admission', CWE::class));
    }

    public function getDiagnosticRelatedGroup(): CNE
    {
        return $this->getField(1)->getInstance();
    }

    public function getAssignedDateTime(): DTM
    {
        return $this->getField(2)->getInstance();
    }

    public function getApprovalIndicator(): ID
    {
        return $this->getField(3)->getInstance();
    }

    public function getGrouperReviewCode(): CWE
    {
        return $this->getField(4)->getInstance();
    }

    public function getOutlierType(): CWE
    {
        return $this->getField(5)->getInstance();
    }

    public function getOutlierDays(): NM
    {
        return $this->getField(6)->getInstance();
    }

    public function getOutlierCost(): CP
    {
        return $this->getField(7)->getInstance();
    }

    public function getPayor(): CWE
    {
        return $this->getField(8)->getInstance();
    }

    public function getOutlierReimbursement(): CP
    {
        return $this->getField(9)->getInstance();
    }

    public function getConfidentialIndicator(): ID
    {
        return $this->getField(10)->getInstance();
    }

    public function getTransferType(): CWE
    {
        return $this->getField(11)->getInstance();
    }

    public function getNameOfCoder(): XPN
    {
        return $this->getField(12)->getInstance();
    }

    public function getGrouperStatus(): CWE
    {
        return $this->getField(13)->getInstance();
    }

    public function getPcclValueCode(): CWE
    {
        return $this->getField(14)->getInstance();
    }

    public function getEffectiveWeight(): NM
    {
        return $this->getField(15)->getInstance();
    }

    public function getMonetaryAmount(): MO
    {
        return $this->getField(16)->getInstance();
    }

    public function getStatusPatient(): CWE
    {
        return $this->getField(17)->getInstance();
    }

    public function getGrouperSoftwareName(): ST
    {
        return $this->getField(18)->getInstance();
    }

    public function getGrouperSoftwareVersion(): ST
    {
        return $this->getField(19)->getInstance();
    }

    public function getStatusFinancialCalculation(): CWE
    {
        return $this->getField(20)->getInstance();
    }

    public function getRelativeDiscountSurcharge(): MO
    {
        return $this->getField(21)->getInstance();
    }

    public function getBasicCharge(): MO
    {
        return $this->getField(22)->getInstance();
    }

    public function getTotalCharge(): MO
    {
        return $this->getField(23)->getInstance();
    }

    public function getDiscountSurcharge(): MO
    {
        return $this->getField(24)->getInstance();
    }

    public function getCalculatedDays(): NM
    {
        return $this->getField(25)->getInstance();
    }

    public function getStatusGender(): CWE
    {
        return $this->getField(26)->getInstance();
    }

    public function getStatusAge(): CWE
    {
        return $this->getField(27)->getInstance();
    }

    public function getStatusLengthOfStay(): CWE
    {
        return $this->getField(28)->getInstance();
    }

    public function getStatusSameDayFlag(): CWE
    {
        return $this->getField(29)->getInstance();
    }

    public function getStatusSeparationMode(): CWE
    {
        return $this->getField(30)->getInstance();
    }

    public function getStatusWeightAtBirth(): CWE
    {
        return $this->getField(31)->getInstance();
    }

    public function getStatusRespirationMinutes(): CWE
    {
        return $this->getField(32)->getInstance();
    }

    public function getStatusAdmission(): CWE
    {
        return $this->getField(33)->getInstance();
    }
}
