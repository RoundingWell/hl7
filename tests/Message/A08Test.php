<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Message;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Message\A08;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;

#[CoversClass(A08::class)]
final class A08Test extends TestCase
{
    private A08 $message;

    #[Override]
    protected function setUp(): void
    {
        $this->message = new A08([
            new MSH(),
            new EVN(),
            new PID(),
            new PV1(),
        ]);
    }

    public function testGetEvnReturnsTheEventTypeSegment(): void
    {
        // A08 updates patient information; the EVN describing the update is required.
        $this->assertInstanceOf(EVN::class, $this->message->getEVN());
    }

    public function testGetPidReturnsThePatientIdentificationSegment(): void
    {
        // The updated demographics live on PID, which is always present and must resolve without an optional check.
        $this->assertInstanceOf(PID::class, $this->message->getPID());
    }

    public function testGetPv1ReturnsThePatientVisitSegment(): void
    {
        // A08 carries the associated visit context; PV1 is required and must be reachable as a typed segment.
        $this->assertInstanceOf(PV1::class, $this->message->getPV1());
    }
}
