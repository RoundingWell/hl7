<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Message\ADT;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Message\ADT\A01;
use RoundingWell\HL7\Message\ADT\A04;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;

#[CoversClass(A04::class)]
final class A04Test extends TestCase
{
    private function parse(): A04
    {
        $message = new A04();
        $message->parse(new Encoding("\r"), "MSH|^~\\&\rEVN|A04\rPID|1\rPV1|1");

        return $message;
    }

    public function testReusesTheA01MessageStructure(): void
    {
        // Per the HAPI event map, ADT_A04 shares the ADT_A01 structure; A04 must therefore be
        // usable everywhere an A01 is, inheriting its structure definition and typed accessors.
        $this->assertInstanceOf(A01::class, $this->parse());
    }

    public function testExposesTheInheritedTypedSegmentAccessors(): void
    {
        // The reuse only matters if the inherited accessors actually resolve the A04's own segments;
        // parsing an A04 event must yield the same typed EVN/PID/PV1 an A01 provides.
        $message = $this->parse();

        $this->assertInstanceOf(EVN::class, $message->getEVN());
        $this->assertInstanceOf(PID::class, $message->getPID());
        $this->assertInstanceOf(PV1::class, $message->getPV1());
    }
}
