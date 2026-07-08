<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\DG1;
use RoundingWell\HL7\Segment\DRG;
use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\Segment\NK1;
use RoundingWell\HL7\Segment\OBX;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\Segment\PV1;
use RoundingWell\HL7\Segment\PV2;
use RoundingWell\HL7\SegmentFactory;

/**
 * @mago-expect lint:too-many-methods
 */
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

    public function testShouldCreatePidSegment(): void
    {
        $segment = $this->factory->parse('PID|1||10006579');

        $this->assertInstanceOf(PID::class, $segment);
    }

    public function testShouldCreateNk1Segment(): void
    {
        $segment = $this->factory->parse('NK1|1|DUCK^DAISY');

        $this->assertInstanceOf(NK1::class, $segment);
    }

    public function testShouldCreatePv1Segment(): void
    {
        $segment = $this->factory->parse('PV1|1|I');

        $this->assertInstanceOf(PV1::class, $segment);
    }

    public function testShouldCreatePv2Segment(): void
    {
        $segment = $this->factory->parse('PV2|PtCare');

        $this->assertInstanceOf(PV2::class, $segment);
    }

    public function testShouldCreateDg1Segment(): void
    {
        $segment = $this->factory->parse('DG1|1||A01.1');

        $this->assertInstanceOf(DG1::class, $segment);
    }

    public function testShouldCreateDrgSegment(): void
    {
        $segment = $this->factory->parse('DRG|G001');

        $this->assertInstanceOf(DRG::class, $segment);
    }

    public function testShouldCreateObxSegment(): void
    {
        $segment = $this->factory->parse('OBX|1|ST');

        $this->assertInstanceOf(OBX::class, $segment);
    }

    public function testShouldCreateGenericSegmentWithId(): void
    {
        $segment = $this->factory->parse('ZZ1|1|DUCK^DAISY');

        $this->assertSame('ZZ1', $segment->getId());
    }
}
