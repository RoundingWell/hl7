<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Message;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Message\A01;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;

#[CoversClass(A01::class)]
final class A01Test extends TestCase
{
    private A01 $message;

    #[Override]
    protected function setUp(): void
    {
        $this->message = new A01([
            new MSH(),
            new EVN(),
            new PID(),
            new PV1(),
        ]);
    }

    public function testGetEvnReturnsTheEventTypeSegment(): void
    {
        // A01 is an Admit event; the EVN describing it is required and must be directly accessible.
        $this->assertInstanceOf(EVN::class, $this->message->getEVN());
    }

    public function testGetPidReturnsThePatientIdentificationSegment(): void
    {
        // An admit always concerns a patient; PID access must resolve without an optional check.
        $this->assertInstanceOf(PID::class, $this->message->getPID());
    }

    public function testGetPv1ReturnsThePatientVisitSegment(): void
    {
        // The visit being admitted is required; PV1 must be reachable as a typed segment.
        $this->assertInstanceOf(PV1::class, $this->message->getPV1());
    }
}
