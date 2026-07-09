<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\BaseMessage;
use RoundingWell\HL7\BaseSegment;
use RoundingWell\HL7\Exception\InvalidSegment;
use RoundingWell\HL7\Segment\MSH;

#[CoversClass(BaseMessage::class)]
final class BaseMessageTest extends TestCase
{
    private BaseMessage $message;

    protected function setUp(): void
    {
        $this->message = new BaseMessage([
            new MSH(),
            new BaseSegment('DG1'),
            new BaseSegment('DG1'),
        ]);
    }

    public function testGetSegmentReturnsTheFirstMatchById(): void
    {
        $segment = $this->message->getSegment('DG1');

        $this->assertInstanceOf(BaseSegment::class, $segment);
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
        $this->expectExceptionMessageIsOrContains("Segment 'BaseMessage.ZZZ' is not defined");

        $this->message->getRequiredSegment('ZZZ');
    }

    public function testGetAllSegmentsReturnsEveryMatchInOrder(): void
    {
        // Repeating segments (e.g. multiple diagnoses) must all be retrievable.
        $segments = $this->message->getAllSegments('DG1');

        $this->assertCount(2, $segments);
    }
}
