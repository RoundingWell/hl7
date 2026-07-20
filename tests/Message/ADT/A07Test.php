<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Message\ADT;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Message\ADT\A06;
use RoundingWell\HL7\Message\ADT\A07;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;

#[CoversClass(A07::class)]
final class A07Test extends TestCase
{
    private function parse(string $middle = ''): A07
    {
        $message = new A07();
        $message->parse(new Encoding("\r"), "MSH|^~\\&\rEVN|A07\rPID|1{$middle}\rPV1|1");

        return $message;
    }

    public function testReusesTheA06MessageStructure(): void
    {
        // Per the HAPI event map, ADT_A07 shares the ADT_A06 structure; A07 must therefore be
        // usable everywhere an A06 is, inheriting its structure definition and typed accessors.
        $this->assertInstanceOf(A06::class, $this->parse());
    }

    public function testExposesTheInheritedTypedSegmentAccessors(): void
    {
        // The reuse only matters if the inherited accessors actually resolve the A07's own segments;
        // parsing an A07 event must yield the same typed EVN/PID/PV1 an A06 provides.
        $message = $this->parse();

        $this->assertInstanceOf(EVN::class, $message->getEVN());
        $this->assertInstanceOf(PID::class, $message->getPID());
        $this->assertInstanceOf(PV1::class, $message->getPV1());
    }

    public function testRetainsTheMrgSegmentUniqueToTheA06Structure(): void
    {
        // MRG is the segment the A06 structure adds over A01; retaining it proves A07 inherits the
        // A06 structure specifically, not merely a generic ADT layout.
        $this->assertCount(1, $this->parse("\rMRG|10006580")->getAll('MRG'));
    }
}
