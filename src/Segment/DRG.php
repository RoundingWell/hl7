<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\DataType\CNE;
use RoundingWell\HL7\DataType\CP;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\MO;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XPN;
use RoundingWell\HL7\Field;
use RoundingWell\HL7\Segment;

/**
 * Diagnosis Related Group Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class DRG extends Segment
{
    public function __construct()
    {
        parent::__construct('DRG');

        $this->addField(1, new Field('Diagnostic Related Group', CNE::class));
        $this->addField(2, new Field('DRG Assigned Date/Time', DTM::class));
        $this->addField(3, new Field('DRG Approval Indicator', ID::class, args: ['table' => 136]));
        $this->addField(4, new Field('DRG Grouper Review Code', CWE::class));
        $this->addField(5, new Field('Outlier Type', CWE::class));
        $this->addField(6, new Field('Outlier Days', NM::class));
        $this->addField(7, new Field('Outlier Cost', CP::class));
        $this->addField(8, new Field('DRG Payor', CWE::class));
        $this->addField(9, new Field('Outlier Reimbursement', CP::class));
        $this->addField(10, new Field('Confidential Indicator', ID::class, args: ['table' => 136]));
        $this->addField(11, new Field('DRG Transfer Type', CWE::class));
        $this->addField(12, new Field('Name of Coder', XPN::class));
        $this->addField(13, new Field('Grouper Status', CWE::class));
        $this->addField(14, new Field('PCCL Value Code', CWE::class));
        $this->addField(15, new Field('Effective Weight', NM::class));
        $this->addField(16, new Field('Monetary Amount', MO::class));
        $this->addField(17, new Field('Status Patient', CWE::class));
        $this->addField(18, new Field('Grouper Software Name', ST::class));
        $this->addField(19, new Field('Grouper Software Version', ST::class));
        $this->addField(20, new Field('Status Financial Calculation', CWE::class));
        $this->addField(21, new Field('Relative Discount/Surcharge', MO::class));
        $this->addField(22, new Field('Basic Charge', MO::class));
        $this->addField(23, new Field('Total Charge', MO::class));
        $this->addField(24, new Field('Discount/Surcharge', MO::class));
        $this->addField(25, new Field('Calculated Days', NM::class));
        $this->addField(26, new Field('Status Gender', CWE::class));
        $this->addField(27, new Field('Status Age', CWE::class));
        $this->addField(28, new Field('Status Length of Stay', CWE::class));
        $this->addField(29, new Field('Status Same Day Flag', CWE::class));
        $this->addField(30, new Field('Status Separation Mode', CWE::class));
        $this->addField(31, new Field('Status Weight at Birth', CWE::class));
        $this->addField(32, new Field('Status Respiration Minutes', CWE::class));
        $this->addField(33, new Field('Status Admission', CWE::class));
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
