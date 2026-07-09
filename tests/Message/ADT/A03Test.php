<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Message\ADT;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Message\ADT\A03;
use RoundingWell\HL7\Segment\DG1;
use RoundingWell\HL7\Segment\DRG;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\NK1;
use RoundingWell\HL7\Segment\OBX;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;
use RoundingWell\HL7\Segment\PV2;

#[CoversClass(A03::class)]
final class A03Test extends TestCase
{
    private Encoding $encoding;

    protected function setUp(): void
    {
        $this->encoding = new Encoding("\r");
    }

    /**
     * @param list<string> $beforePv1 segment lines inserted before PV1, in HAPI structure order
     * @param list<string> $afterPv1  segment lines inserted after PV1, in HAPI structure order
     */
    private function parse(array $beforePv1 = [], array $afterPv1 = []): A03
    {
        $lines = ['MSH|^~\\&', 'EVN|A03', 'PID|1', ...$beforePv1, 'PV1|1', ...$afterPv1];

        $message = new A03();
        $message->parse($this->encoding, implode("\r", $lines));

        return $message;
    }

    public function testGetEvnReturnsTheEventTypeSegment(): void
    {
        // A03 is a Discharge event; the EVN describing it is required and must be directly accessible.
        $this->assertInstanceOf(EVN::class, $this->parse()->getEVN());
    }

    public function testGetPidReturnsThePatientIdentificationSegment(): void
    {
        // A discharge always concerns a patient; PID access must resolve without an optional check.
        $this->assertInstanceOf(PID::class, $this->parse()->getPID());
    }

    public function testGetPv1ReturnsThePatientVisitSegment(): void
    {
        // The visit being discharged is required; PV1 must be reachable as a typed segment.
        $this->assertInstanceOf(PV1::class, $this->parse()->getPV1());
    }

    public function testGetPv2ReturnsThePatientVisitAdditionalInformationSegment(): void
    {
        // PV2 carries additional visit detail; when present it must be reachable as a typed segment.
        $message = new A03();
        $message->parse($this->encoding, "MSH|^~\\&\rEVN|A03\rPID|1\rPV1|1\rPV2|1");

        $this->assertInstanceOf(PV2::class, $message->getPV2());
    }

    public function testGetPv2ReturnsNullWhenTheSegmentIsAbsent(): void
    {
        // PV2 is optional; its absence is a normal state that must yield null, not an empty phantom.
        $this->assertNull($this->parse()->getPV2());
    }

    public function testGetDrgReturnsTheDiagnosisRelatedGroupSegment(): void
    {
        // DRG classifies the visit for billing; when present it must be reachable as a typed segment.
        $message = new A03();
        $message->parse($this->encoding, "MSH|^~\\&\rEVN|A03\rPID|1\rPV1|1\rDRG|1");

        $this->assertInstanceOf(DRG::class, $message->getDRG());
    }

    public function testGetDrgReturnsNullWhenTheSegmentIsAbsent(): void
    {
        // DRG is optional; its absence must yield null, not an error.
        $this->assertNull($this->parse()->getDRG());
    }

    public function testListNk1ReturnsEveryNextOfKinSegment(): void
    {
        // NK1 repeats; every occurrence must be returned so no associated party is lost.
        $nk1 = $this->parse(beforePv1: ['NK1|1', 'NK1|2'])->listNK1();

        $this->assertCount(2, $nk1);
        $this->assertContainsOnlyInstancesOf(NK1::class, $nk1);
    }

    public function testListNk1ReturnsAnEmptyListWhenNoneArePresent(): void
    {
        // With no NK1 segments the list must be empty, never null, so callers can iterate safely.
        $this->assertSame([], $this->parse()->listNK1());
    }

    public function testListObxReturnsEveryObservationSegment(): void
    {
        // OBX repeats; every observation must be returned so no result is lost.
        $obx = $this->parse(afterPv1: ['OBX|1', 'OBX|2'])->listOBX();

        $this->assertCount(2, $obx);
        $this->assertContainsOnlyInstancesOf(OBX::class, $obx);
    }

    public function testListObxReturnsAnEmptyListWhenNoneArePresent(): void
    {
        // With no OBX segments the list must be empty, never null.
        $this->assertSame([], $this->parse()->listOBX());
    }

    public function testListDg1ReturnsEveryDiagnosisSegment(): void
    {
        // DG1 repeats; every diagnosis must be returned so no diagnosis is lost.
        $dg1 = $this->parse(afterPv1: ['DG1|1', 'DG1|2'])->listDG1();

        $this->assertCount(2, $dg1);
        $this->assertContainsOnlyInstancesOf(DG1::class, $dg1);
    }

    public function testListDg1ReturnsAnEmptyListWhenNoneArePresent(): void
    {
        // With no DG1 segments the list must be empty, never null.
        $this->assertSame([], $this->parse()->listDG1());
    }

    public function testArvAndRolAreRetainedAtBothStructuralPositions(): void
    {
        // HAPI lists ARV and ROL both before PV1 and again after PV2. Each occurrence must be
        // retained at its own position ('ARV'/'ROL' before, 'ARV2'/'ROL2' after) so no access
        // restriction or role is lost and positions are never merged.
        $message = $this->parse(beforePv1: ['ARV|1', 'ROL|1'], afterPv1: ['ARV|2', 'ROL|2']);

        $this->assertCount(1, $message->getAll('ARV'));
        $this->assertCount(1, $message->getAll('ROL'));
        $this->assertCount(1, $message->getAll('ARV2'));
        $this->assertCount(1, $message->getAll('ROL2'));
    }
}
