<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\HD;
use RoundingWell\HL7\Encoding;

#[CoversClass(HD::class)]
final class HDTest extends TestCase
{
    public function testComponentsMapToNamespaceAndUniversalId(): void
    {
        // A hierarchic designator names an authority via namespace and universal id.
        $hd = new HD();
        $hd->parse(new Encoding(), 'AccMgr^1.2.840^ISO');

        $this->assertSame('AccMgr', $hd->getNamespaceId()->getValue());
        $this->assertSame('1.2.840', $hd->getUniversalId()->getValue());
        $this->assertSame('ISO', $hd->getUniversalIdType()->getValue());
    }
}
