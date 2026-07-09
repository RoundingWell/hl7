<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\GenericGroup;

#[CoversClass(GenericGroup::class)]
final class GenericGroupTest extends TestCase
{
    public function testExposesTheNameItWasConstructedWith(): void
    {
        // The group name identifies the structure in error messages and message routing, so it must round-trip.
        $this->assertSame('GRP', new GenericGroup('GRP')->getName());
    }
}
