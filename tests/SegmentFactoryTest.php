<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\SegmentFactory;

#[CoversClass(SegmentFactory::class)]
final class SegmentFactoryTest extends TestCase
{
    private SegmentFactory $factory;

    #[Override]
    protected function setUp(): void
    {
        $this->factory = new SegmentFactory(new Encoding());
    }

    public function testShouldCreateMshSegment(): void
    {
        $segment = $this->factory->parse('MSH|^~\\&|AccMgr|1');

        $this->assertInstanceOf(MSH::class, $segment);
    }

    public function testShouldCreateEvnSegment(): void
    {
        $segment = $this->factory->parse('EVN|A01|20050110045502');

        $this->assertInstanceOf(EVN::class, $segment);
    }

    public function testShouldCreateGenericSegmentWithId(): void
    {
        $segment = $this->factory->parse('PID|1||10006579');

        $this->assertSame('PID', $segment->getId());
    }
}
