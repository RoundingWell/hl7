<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Message;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Message\A06;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;

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
}
