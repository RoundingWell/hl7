<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Message;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Message\A06;
use RoundingWell\HL7\Segment\DG1;
use RoundingWell\HL7\Segment\DRG;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\Segment\NK1;
use RoundingWell\HL7\Segment\OBX;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;
use RoundingWell\HL7\Segment\PV2;

/**
 * @mago-expect lint:too-many-methods
 */
#[CoversClass(A06::class)]
final class A06Test extends TestCase
{
    private A06 $message;

    #[Override]
    protected function setUp(): void
    {
        $this->message = new A06([
            new MSH(),
            new EVN(),
            new PID(),
            new PV1(),
        ]);
    }

    public function testGetEvnReturnsTheEventTypeSegment(): void
    {
        // A06 changes an outpatient to an inpatient; the EVN describing the change is required.
        $this->assertInstanceOf(EVN::class, $this->message->getEVN());
    }

    public function testGetPidReturnsThePatientIdentificationSegment(): void
    {
        // The patient whose status changes is always identified; PID must resolve without an optional check.
        $this->assertInstanceOf(PID::class, $this->message->getPID());
    }

    public function testGetPv1ReturnsThePatientVisitSegment(): void
    {
        // The visit whose patient class changes is required; PV1 must be reachable as a typed segment.
        $this->assertInstanceOf(PV1::class, $this->message->getPV1());
    }

    public function testGetPv2ReturnsThePatientVisitAdditionalInformationSegment(): void
    {
        // PV2 carries additional visit detail; when present it must be reachable as a typed segment.
        $message = new A06([new MSH(), new EVN(), new PID(), new PV1(), new PV2()]);

        $this->assertInstanceOf(PV2::class, $message->getPV2());
    }

    public function testGetPv2ReturnsNullWhenTheSegmentIsAbsent(): void
    {
        // PV2 is optional; its absence is a normal state that must yield null, not an error.
        $this->assertNull($this->message->getPV2());
    }

    public function testGetDrgReturnsTheDiagnosisRelatedGroupSegment(): void
    {
        // DRG classifies the visit for billing; when present it must be reachable as a typed segment.
        $message = new A06([new MSH(), new EVN(), new PID(), new PV1(), new DRG()]);

        $this->assertInstanceOf(DRG::class, $message->getDRG());
    }

    public function testGetDrgReturnsNullWhenTheSegmentIsAbsent(): void
    {
        // DRG is optional; its absence must yield null, not an error.
        $this->assertNull($this->message->getDRG());
    }

    public function testListNk1ReturnsEveryNextOfKinSegment(): void
    {
        // NK1 repeats; every occurrence must be returned so no associated party is lost.
        $message = new A06([new MSH(), new EVN(), new PID(), new PV1(), new NK1(), new NK1()]);

        $nk1 = $message->listNK1();

        $this->assertCount(2, $nk1);
        $this->assertContainsOnlyInstancesOf(NK1::class, $nk1);
    }

    public function testListNk1ReturnsAnEmptyListWhenNoneArePresent(): void
    {
        // With no NK1 segments the list must be empty, never null, so callers can iterate safely.
        $this->assertSame([], $this->message->listNK1());
    }

    public function testListObxReturnsEveryObservationSegment(): void
    {
        // OBX repeats; every observation must be returned so no result is lost.
        $message = new A06([new MSH(), new EVN(), new PID(), new PV1(), new OBX(), new OBX()]);

        $obx = $message->listOBX();

        $this->assertCount(2, $obx);
        $this->assertContainsOnlyInstancesOf(OBX::class, $obx);
    }

    public function testListObxReturnsAnEmptyListWhenNoneArePresent(): void
    {
        // With no OBX segments the list must be empty, never null.
        $this->assertSame([], $this->message->listOBX());
    }

    public function testListDg1ReturnsEveryDiagnosisSegment(): void
    {
        // DG1 repeats; every diagnosis must be returned so no diagnosis is lost.
        $message = new A06([new MSH(), new EVN(), new PID(), new PV1(), new DG1(), new DG1()]);

        $dg1 = $message->listDG1();

        $this->assertCount(2, $dg1);
        $this->assertContainsOnlyInstancesOf(DG1::class, $dg1);
    }

    public function testListDg1ReturnsAnEmptyListWhenNoneArePresent(): void
    {
        // With no DG1 segments the list must be empty, never null.
        $this->assertSame([], $this->message->listDG1());
    }
}
