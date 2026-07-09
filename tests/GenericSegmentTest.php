<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\GenericSegment;

#[CoversClass(GenericSegment::class)]
final class GenericSegmentTest extends TestCase
{
    public function testExposesTheNameItWasConstructedWith(): void
    {
        // The segment identifier drives error messages and message routing, so it must round-trip verbatim.
        $this->assertSame('ZZZ', new GenericSegment('ZZZ')->getName());
    }
}
