<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\OBX;

#[CoversClass(OBX::class)]
final class OBXTest extends TestCase
{
    private OBX $obx;

    #[Override]
    protected function setUp(): void
    {
        $this->obx = new OBX();
        $this->obx->setRaw(new Encoding(), [
            '1', // OBX.1 Set ID
            'NM', // OBX.2 Value Type
            '8480-6^Systolic BP^LN', // OBX.3 Observation Identifier
            '1', // OBX.4 Observation Sub-ID
            '120~80', // OBX.5 Observation Value (repeating)
            'mm[Hg]^millimeters of mercury', // OBX.6 Units
            '90-120', // OBX.7 References Range
            'N^Normal~A^Abnormal', // OBX.8 Interpretation Codes (repeating)
            '0.95', // OBX.9 Probability
            'B~W', // OBX.10 Nature of Abnormal Test (repeating)
            'F', // OBX.11 Observation Result Status
            '20050110', // OBX.12 Effective Date of Reference Range
            'CHECK1', // OBX.13 User Defined Access Checks
            '20050110045502', // OBX.14 Date/Time of the Observation
            'PROD^Producer', // OBX.15 Producer's ID
            '1234^WELBY^MARCUS~5678^HOUSE^GREGORY', // OBX.16 Responsible Observer (repeating)
            'MANUAL^Manual~AUTO^Automated', // OBX.17 Observation Method (repeating)
            'EQ1^^^ISO~EQ2^^^ISO', // OBX.18 Equipment Instance Identifier (repeating)
            '20050111', // OBX.19 Date/Time of the Analysis
            'LARM^Left Arm~RARM^Right Arm', // OBX.20 Observation Site (repeating)
            'OBS1^^^ISO', // OBX.21 Observation Instance Identifier
            'EVN', // OBX.22 Mood Code
            'General Hospital^^^^^^^^ORG', // OBX.23 Performing Organization Name
            '100 MAIN ST^^METROPOLIS^NY^10001', // OBX.24 Performing Organization Address
            '9999^STRANGE^STEPHEN', // OBX.25 Performing Organization Medical Director
            'SD', // OBX.26 Patient Results Release Category
            'ROOT^Root Cause', // OBX.27 Root Cause
            'LPC1^Control One~LPC2^Control Two', // OBX.28 Local Process Control (repeating)
        ]);
    }

    public function testSequenceAndStatusFieldsMapToTheirValues(): void
    {
        // The Set ID orders repeated observations; the result status governs whether a value is final.
        $this->assertSame('1', $this->obx->getIdentity()->getValue());
        $this->assertSame('NM', $this->obx->getValueType()->getValue());
        $this->assertSame('1', $this->obx->getObservationSubId()->getValue());
        $this->assertSame('F', $this->obx->getObservationResultStatus()->getValue());
        $this->assertSame('SD', $this->obx->getPatientResultsReleaseCategory()->getValue());
    }

    public function testObservationValuesCollectEachRepetition(): void
    {
        // OBX.5 is repeating; each measured value must be retained in order.
        $values = $this->obx->getObservationValue();
        $this->assertCount(2, $values);
        $this->assertSame('120', $values[0]->getValue());
        $this->assertSame('80', $values[1]->getValue());
    }

    public function testCodedFieldsMapToTheirLeadingIdentifier(): void
    {
        $this->assertSame('8480-6', $this->obx->getObservationIdentifier()->identifier->getValue());
        $this->assertSame('mm[Hg]', $this->obx->getUnits()->identifier->getValue());
        $this->assertSame('PROD', $this->obx->getProducerId()->identifier->getValue());
        $this->assertSame('ROOT', $this->obx->getRootCause()->identifier->getValue());
    }

    public function testRepeatingCodedFieldsCollectEachEntry(): void
    {
        $interpretations = $this->obx->getInterpretationCodes();
        $this->assertCount(2, $interpretations);
        $this->assertSame('N', $interpretations[0]->identifier->getValue());
        $this->assertSame('A', $interpretations[1]->identifier->getValue());

        $this->assertSame('MANUAL', $this->obx->getObservationMethod()[0]->identifier->getValue());
        $this->assertSame('LARM', $this->obx->getObservationSite()[0]->identifier->getValue());
        $this->assertSame('LPC1', $this->obx->getLocalProcessControl()[0]->identifier->getValue());
    }

    public function testAbnormalFlagsCollectEachRepetition(): void
    {
        // OBX.10 is repeating; every abnormal-test flag must be preserved in order.
        $flags = $this->obx->getNatureOfAbnormalTest();
        $this->assertCount(2, $flags);
        $this->assertSame('B', $flags[0]->getValue());
        $this->assertSame('W', $flags[1]->getValue());
    }

    public function testDateTimeFieldsMapToTheirValues(): void
    {
        $this->assertSame('20050110', $this->obx->getEffectiveDateOfReferenceRange()->getValue());
        $this->assertSame('20050110045502', $this->obx->getObservationDateTime()->getValue());
        $this->assertSame('20050111', $this->obx->getAnalysisDateTime()->getValue());
    }

    public function testFreeTextAndNumericFieldsMapToTheirValues(): void
    {
        $this->assertSame('90-120', $this->obx->getReferencesRange()->getValue());
        $this->assertSame('0.95', $this->obx->getProbability()->getValue());
        $this->assertSame('CHECK1', $this->obx->getUserDefinedAccessChecks()->getValue());
    }

    public function testResponsibleObserversCollectEachRepetition(): void
    {
        // OBX.16 is repeating; each observer reference must be retained in order.
        $observers = $this->obx->getResponsibleObserver();
        $this->assertCount(2, $observers);
        $this->assertSame('1234', $observers[0]->id->getValue());
        $this->assertSame('WELBY', $observers[0]->familyName->surname->getValue());
        $this->assertSame('5678', $observers[1]->id->getValue());
    }

    public function testEquipmentIdentifiersCollectEachRepetition(): void
    {
        // OBX.18 is repeating; each producing device must be identifiable in order.
        $equipment = $this->obx->getEquipmentInstanceIdentifier();
        $this->assertCount(2, $equipment);
        $this->assertSame('EQ1', $equipment[0]->id->getValue());
        $this->assertSame('EQ2', $equipment[1]->id->getValue());

        $this->assertSame('OBS1', $this->obx->getObservationInstanceIdentifier()->id->getValue());
    }

    public function testPerformingOrganizationFieldsMapToTheirComponents(): void
    {
        $this->assertSame('EVN', $this->obx->getMoodCode()->identifier->getValue());
        $this->assertSame('General Hospital', $this->obx->getPerformingOrganizationName()->name->getValue());
        $this->assertSame(
            '100 MAIN ST',
            $this->obx->getPerformingOrganizationAddress()->streetAddress->streetAddress->getValue(),
        );
        $this->assertSame(
            'STRANGE',
            $this->obx->getPerformingOrganizationMedicalDirector()->familyName->surname->getValue(),
        );
    }
}
