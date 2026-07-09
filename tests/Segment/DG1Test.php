<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\DG1;

#[CoversClass(DG1::class)]
final class DG1Test extends TestCase
{
    private DG1 $dg1;

    #[Override]
    protected function setUp(): void
    {
        $this->dg1 = new DG1();
        $this->dg1->parse(new Encoding(), implode('|', [
            'DG1', // Segment name
            '1', // DG1.1 Set ID
            'I10', // DG1.2 Diagnosis Coding Method
            'E11.9^Type 2 diabetes mellitus without complications^I10', // DG1.3 Diagnosis Code
            'Type 2 diabetes mellitus', // DG1.4 Diagnosis Description
            '20240115', // DG1.5 Diagnosis Date/Time
            'A^Admitting', // DG1.6 Diagnosis Type
            'MDC10^Endocrine^MDC', // DG1.7 Major Diagnostic Category
            '638^Diabetes^DRG', // DG1.8 Diagnostic Related Group
            'Y', // DG1.9 DRG Approval Indicator
            '0^Reviewed', // DG1.10 DRG Grouper Review Code
            'DAY^Day Outlier', // DG1.11 Outlier Type
            '3', // DG1.12 Outlier Days
            '150.00^DAILY', // DG1.13 Outlier Cost
            'v40', // DG1.14 Grouper Version And Type
            '1', // DG1.15 Diagnosis Priority
            '1234^HOUSE^GREGORY~5678^WATSON^JOHN', // DG1.16 Diagnosing Clinician (repeating)
            'C^Confirmed', // DG1.17 Diagnosis Classification
            'N', // DG1.18 Confidential Indicator
            '20240116', // DG1.19 Attestation Date/Time
            'DIAG123^^^ISO', // DG1.20 Diagnosis Identifier
            'A', // DG1.21 Diagnosis Action Code
            'DIAG100^^^ISO', // DG1.22 Parent Diagnosis
            'CCL2^Complication^CCL', // DG1.23 DRG CCL Value Code
            'N', // DG1.24 DRG Grouping Usage
            'F^Final', // DG1.25 DRG Diagnosis Determination Status
            'Y^Yes', // DG1.26 Present On Admission (POA) Indicator
        ]));
    }

    public function testSequenceAndPriorityFieldsMapToTheirValues(): void
    {
        // The Set ID orders repeating diagnoses and the priority ranks their significance.
        $this->assertSame('1', $this->dg1->getIdentity()->getValue());
        $this->assertSame('1', $this->dg1->getPriority()->getValue());
        $this->assertSame('3', $this->dg1->getOutlierDays()->getValue());
    }

    public function testCodedDiagnosisFieldsMapToTheirLeadingIdentifier(): void
    {
        // The diagnosis code and its type drive downstream billing and clinical logic.
        $this->assertSame('E11.9', $this->dg1->getCode()->getIdentifier()->getValue());
        $this->assertSame('A', $this->dg1->getType()->getIdentifier()->getValue());
        $this->assertSame('0', $this->dg1->getDrgGrouperReviewCode()->getIdentifier()->getValue());
        $this->assertSame('DAY', $this->dg1->getOutlierType()->getIdentifier()->getValue());
        $this->assertSame('C', $this->dg1->getClassification()->getIdentifier()->getValue());
        $this->assertSame('CCL2', $this->dg1->getDrgCclValueCode()->getIdentifier()->getValue());
        $this->assertSame('F', $this->dg1->getDrgDiagnosisDeterminationStatus()->getIdentifier()->getValue());
        $this->assertSame('Y', $this->dg1->getPresentOnAdmissionIndicator()->getIdentifier()->getValue());
    }

    public function testDiagnosticGroupingFieldsMapToTheirIdentifiers(): void
    {
        // MDC and DRG classify the diagnosis for reimbursement grouping.
        $this->assertSame('MDC10', $this->dg1->getMajorDiagnosticCategory()->getIdentifier()->getValue());
        $this->assertSame('638', $this->dg1->getDiagnosticRelatedGroup()->getIdentifier()->getValue());
    }

    public function testFreeTextAndMethodFieldsMapToTheirValues(): void
    {
        $this->assertSame('I10', $this->dg1->getCodingMethod()->getValue());
        $this->assertSame('Type 2 diabetes mellitus', $this->dg1->getDescription()->getValue());
        $this->assertSame('v40', $this->dg1->getGrouperVersionAndType()->getValue());
    }

    public function testDateFieldsMapToTheirValues(): void
    {
        $this->assertSame('20240115', $this->dg1->getDateTime()->getValue());
        $this->assertSame('20240116', $this->dg1->getAttestationDateTime()->getValue());
    }

    public function testIndicatorFieldsMapToTheirValues(): void
    {
        $this->assertSame('Y', $this->dg1->getDrgApprovalIndicator()->getValue());
        $this->assertSame('N', $this->dg1->getConfidentialIndicator()->getValue());
        $this->assertSame('A', $this->dg1->getActionCode()->getValue());
        $this->assertSame('N', $this->dg1->getDrgGroupingUsage()->getValue());
    }

    public function testOutlierCostMapsToItsNestedPrice(): void
    {
        // The outlier cost is a composite price whose money amount is itself a nested component.
        $this->assertSame('150.00', $this->dg1->getOutlierCost()->getPrice()->getQuantity()->getValue());
        $this->assertSame('DAILY', $this->dg1->getOutlierCost()->getPriceType()->getValue());
    }

    public function testDiagnosingClinicianCollectsEveryRepetition(): void
    {
        // DG1.16 is repeating, so every clinician credited with the diagnosis must be retained in order.
        $clinicians = $this->dg1->getDiagnosingClinician();

        $this->assertCount(2, $clinicians);
        $this->assertSame('1234', $clinicians[0]->getId()->getValue());
        $this->assertSame('HOUSE', $clinicians[0]->getFamilyName()->getSurname()->getValue());
        $this->assertSame('5678', $clinicians[1]->getId()->getValue());
    }

    public function testEntityIdentifierFieldsMapToTheirComponents(): void
    {
        // The diagnosis identifier and its parent link revisions of the same diagnosis together.
        $this->assertSame('DIAG123', $this->dg1->getDiagnosisIdentifier()->getId()->getValue());
        $this->assertSame('ISO', $this->dg1->getDiagnosisIdentifier()->getUniversalIdType()->getValue());
        $this->assertSame('DIAG100', $this->dg1->getParentDiagnosis()->getId()->getValue());
    }
}
