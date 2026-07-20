<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Message\ADT;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Message\ADT\A01;
use RoundingWell\HL7\Message\ADT\A13;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;

#[CoversClass(A13::class)]
final class A13Test extends TestCase
{
    private function parse(): A13
    {
        $message = new A13();
        $message->parse(new Encoding("\r"), "MSH|^~\\&\rEVN|A13\rPID|1\rPV1|1");

        return $message;
    }

    public function testReusesTheA01MessageStructure(): void
    {
        // Per the HAPI event map, ADT_A13 (cancel discharge) shares the ADT_A01 structure; A13 must
        // therefore be usable everywhere an A01 is, inheriting its structure definition and accessors.
        $this->assertInstanceOf(A01::class, $this->parse());
    }

    public function testExposesTheInheritedTypedSegmentAccessors(): void
    {
        // The reuse only matters if the inherited accessors actually resolve the A13's own segments;
        // parsing an A13 event must yield the same typed EVN/PID/PV1 an A01 provides.
        $message = $this->parse();

        $this->assertInstanceOf(EVN::class, $message->getEVN());
        $this->assertInstanceOf(PID::class, $message->getPID());
        $this->assertInstanceOf(PV1::class, $message->getPV1());
    }
}
