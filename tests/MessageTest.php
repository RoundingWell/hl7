<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\InvalidSegment;
use RoundingWell\HL7\Message;
use RoundingWell\HL7\Segment;
use RoundingWell\HL7\Segment\MSH;

#[CoversClass(Message::class)]
final class MessageTest extends TestCase
{
    private Message $message;

    protected function setUp(): void
    {
        $this->message = new Message([
            new MSH(),
            new Segment('DG1'),
            new Segment('DG1'),
        ]);
    }

    public function testGetSegmentReturnsTheFirstMatchById(): void
    {
        $segment = $this->message->getSegment('DG1');

        $this->assertInstanceOf(Segment::class, $segment);
        $this->assertSame('DG1', $segment?->getId());
    }

    public function testGetSegmentReturnsNullWhenAbsent(): void
    {
        // Optional segments must be queryable without forcing the caller to catch an exception.
        $this->assertNull($this->message->getSegment('ZZZ'));
    }

    public function testGetMshReturnsTheHeaderSegment(): void
    {
        // Every HL7 message has exactly one MSH; convenience access must resolve it.
        $this->assertInstanceOf(MSH::class, $this->message->getMSH());
    }

    public function testGetRequiredSegmentThrowsWhenAbsent(): void
    {
        // A required segment that is missing is a structural error the caller must know about.
        $this->expectException(InvalidSegment::class);
        $this->expectExceptionMessageIsOrContains("Segment 'Message.ZZZ' is not defined");

        $this->message->getRequiredSegment('ZZZ');
    }

    public function testGetAllSegmentsReturnsEveryMatchInOrder(): void
    {
        // Repeating segments (e.g. multiple diagnoses) must all be retrievable.
        $segments = $this->message->getAllSegments('DG1');

        $this->assertCount(2, $segments);
    }
}
