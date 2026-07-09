<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\SegmentCursor;
use RoundingWell\HL7\SegmentElement;

#[CoversClass(SegmentCursor::class)]
#[CoversClass(SegmentElement::class)]
final class SegmentCursorTest extends TestCase
{
    public function testPeekReturnsCurrentWithoutAdvancing(): void
    {
        // Parsing must look ahead at the next segment without consuming it, so a structure
        // that does not match can leave the segment for a later structure to claim.
        $cursor = new SegmentCursor(new SegmentElement('MSH', 'MSH|'), new SegmentElement('EVN', 'EVN|A01'));

        $this->assertSame('MSH', $cursor->peek()->name);
        $this->assertSame('MSH', $cursor->peek()->name);
    }

    public function testNextReturnsCurrentThenAdvances(): void
    {
        // Consuming a segment must advance exactly one position so each line is parsed once.
        $cursor = new SegmentCursor(new SegmentElement('MSH', 'MSH|'), new SegmentElement('EVN', 'EVN|A01'));

        $this->assertSame('MSH', $cursor->next()->name);
        $this->assertSame('EVN', $cursor->peek()->name);
    }

    public function testValidReportsWhetherSegmentsRemain(): void
    {
        // The parse loop terminates on exhaustion; valid() is its guard.
        $cursor = new SegmentCursor(new SegmentElement('MSH', 'MSH|'));

        $this->assertTrue($cursor->valid());
        $cursor->next();
        $this->assertFalse($cursor->valid());
    }

    public function testPeekThrowsWhenExhausted(): void
    {
        // Reading past the end is a programming error, not a silent empty — surface it.
        $this->expectException(OutOfBoundsException::class);

        new SegmentCursor()->peek();
    }

    public function testNextThrowsWhenExhausted(): void
    {
        // Consuming past the end is a programming error; next() must surface it, not return stale data.
        $this->expectException(OutOfBoundsException::class);

        new SegmentCursor()->next();
    }
}
