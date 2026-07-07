<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Exception;

use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\HL7Exception;
use RoundingWell\HL7\Exception\InvalidSegment;

#[CoversClass(InvalidSegment::class)]
final class InvalidSegmentTest extends TestCase
{
    public function testItIsAnHl7Exception(): void
    {
        // Callers catch HL7Exception to handle any parser failure uniformly.
        $this->assertInstanceOf(HL7Exception::class, InvalidSegment::notDefined('A01', 'PID'));
    }

    public function testItIsAnOutOfBoundsException(): void
    {
        // Requesting a segment the message does not contain is an out-of-range access.
        $this->assertInstanceOf(OutOfBoundsException::class, InvalidSegment::notDefined('A01', 'PID'));
    }

    public function testNotDefinedNamesTheMessageAndSegmentId(): void
    {
        // The message type and missing segment id must appear so the caller knows what was absent.
        $this->assertSame("Segment 'A01.PID' is not defined", InvalidSegment::notDefined('A01', 'PID')->getMessage());
    }
}
