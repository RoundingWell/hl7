<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\DRG;

#[CoversClass(DRG::class)]
final class DRGTest extends TestCase
{
    private DRG $drg;

    #[Override]
    protected function setUp(): void
    {
        $this->drg = new DRG();
        $this->drg->setRaw(new Encoding(), [
            '470^Major Joint^HL70055', // DRG.1 Diagnostic Related Group
            '20050110045502', // DRG.2 DRG Assigned Date/Time
            'Y', // DRG.3 DRG Approval Indicator
            '0^No Review', // DRG.4 DRG Grouper Review Code
            'D^Day Outlier', // DRG.5 Outlier Type
            '3', // DRG.6 Outlier Days
            '1500.00^UP', // DRG.7 Outlier Cost
            'MED^Medicare', // DRG.8 DRG Payor
            '750.00^UP', // DRG.9 Outlier Reimbursement
            'N', // DRG.10 Confidential Indicator
            'T^Transfer In', // DRG.11 DRG Transfer Type
            'DUCK^DONALD', // DRG.12 Name of Coder
            'F^Final', // DRG.13 Grouper Status
            'PCCL2^Level 2', // DRG.14 PCCL Value Code
            '1.2345', // DRG.15 Effective Weight
            '2500.00^USD', // DRG.16 Monetary Amount
            'A^Active', // DRG.17 Status Patient
            'GrouperX', // DRG.18 Grouper Software Name
            '3.5', // DRG.19 Grouper Software Version
            'OK^Calculated', // DRG.20 Status Financial Calculation
            '100.00^USD', // DRG.21 Relative Discount/Surcharge
            '2000.00^USD', // DRG.22 Basic Charge
            '3000.00^USD', // DRG.23 Total Charge
            '50.00^USD', // DRG.24 Discount/Surcharge
            '5', // DRG.25 Calculated Days
            'M^Male OK', // DRG.26 Status Gender
            'AD^Adult', // DRG.27 Status Age
            'IN^In Range', // DRG.28 Status Length of Stay
            'NO^Not Same Day', // DRG.29 Status Same Day Flag
            'HOME^Home', // DRG.30 Status Separation Mode
            'NORM^Normal', // DRG.31 Status Weight at Birth
            'RESP^Respiration', // DRG.32 Status Respiration Minutes
            'EMR^Emergency', // DRG.33 Status Admission
        ]);
    }

    public function testDiagnosticRelatedGroupMapsToItsComponents(): void
    {
        // DRG.1 is the coded group that drives reimbursement, so its identifier and text must resolve.
        $group = $this->drg->getDiagnosticRelatedGroup();
        $this->assertSame('470', $group->identifier->getValue());
        $this->assertSame('Major Joint', $group->text->getValue());
    }

    public function testDateAndIndicatorFieldsMapToTheirValues(): void
    {
        $this->assertSame('20050110045502', $this->drg->getAssignedDateTime()->getValue());
        $this->assertSame('Y', $this->drg->getApprovalIndicator()->getValue());
        $this->assertSame('N', $this->drg->getConfidentialIndicator()->getValue());
    }

    public function testCodedFieldsMapToTheirLeadingIdentifier(): void
    {
        $this->assertSame('0', $this->drg->getGrouperReviewCode()->identifier->getValue());
        $this->assertSame('D', $this->drg->getOutlierType()->identifier->getValue());
        $this->assertSame('MED', $this->drg->getPayor()->identifier->getValue());
        $this->assertSame('T', $this->drg->getTransferType()->identifier->getValue());
        $this->assertSame('F', $this->drg->getGrouperStatus()->identifier->getValue());
        $this->assertSame('PCCL2', $this->drg->getPcclValueCode()->identifier->getValue());
        $this->assertSame('A', $this->drg->getStatusPatient()->identifier->getValue());
        $this->assertSame('OK', $this->drg->getStatusFinancialCalculation()->identifier->getValue());
    }

    public function testGroupingStatusFieldsMapToTheirLeadingIdentifier(): void
    {
        $this->assertSame('M', $this->drg->getStatusGender()->identifier->getValue());
        $this->assertSame('AD', $this->drg->getStatusAge()->identifier->getValue());
        $this->assertSame('IN', $this->drg->getStatusLengthOfStay()->identifier->getValue());
        $this->assertSame('NO', $this->drg->getStatusSameDayFlag()->identifier->getValue());
        $this->assertSame('HOME', $this->drg->getStatusSeparationMode()->identifier->getValue());
        $this->assertSame('NORM', $this->drg->getStatusWeightAtBirth()->identifier->getValue());
        $this->assertSame('RESP', $this->drg->getStatusRespirationMinutes()->identifier->getValue());
        $this->assertSame('EMR', $this->drg->getStatusAdmission()->identifier->getValue());
    }

    public function testNumericFieldsMapToTheirValues(): void
    {
        $this->assertSame('3', $this->drg->getOutlierDays()->getValue());
        $this->assertSame('1.2345', $this->drg->getEffectiveWeight()->getValue());
        $this->assertSame('5', $this->drg->getCalculatedDays()->getValue());
    }

    public function testCompositePriceFieldsMapToTheirNestedAmount(): void
    {
        // Outlier cost and reimbursement are composite prices; the leading price component carries the amount.
        $this->assertSame('1500.00', $this->drg->getOutlierCost()->price->quantity->getValue());
        $this->assertSame('750.00', $this->drg->getOutlierReimbursement()->price->quantity->getValue());
    }

    public function testMonetaryFieldsMapToTheirNestedAmount(): void
    {
        $this->assertSame('2500.00', $this->drg->getMonetaryAmount()->quantity->getValue());
        $this->assertSame('100.00', $this->drg->getRelativeDiscountSurcharge()->quantity->getValue());
        $this->assertSame('2000.00', $this->drg->getBasicCharge()->quantity->getValue());
        $this->assertSame('3000.00', $this->drg->getTotalCharge()->quantity->getValue());
        $this->assertSame('50.00', $this->drg->getDiscountSurcharge()->quantity->getValue());
    }

    public function testCoderAndSoftwareFieldsMapToTheirValues(): void
    {
        $this->assertSame('DUCK', $this->drg->getNameOfCoder()->familyName->surname->getValue());
        $this->assertSame('DONALD', $this->drg->getNameOfCoder()->givenName->getValue());
        $this->assertSame('GrouperX', $this->drg->getGrouperSoftwareName()->getValue());
        $this->assertSame('3.5', $this->drg->getGrouperSoftwareVersion()->getValue());
    }
}
