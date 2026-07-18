<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\HD;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Primitive;

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

    public function testSubcomponentsStayWithinTheComponentThatCarriesThem(): void
    {
        // "a^b&c" is two components: "a" and "b". The "&" is below the component level, so "c"
        // is a subcomponent of the second component, never promoted to a third component.
        $hd = new HD();
        $hd->parse(new Encoding(), 'a^b&c');

        $this->assertSame('a', $hd->getNamespaceId()->getValue());
        $this->assertSame('b', $hd->getUniversalId()->getValue());

        $subcomponents = $hd->getUniversalId()->getExtraComponents();
        $this->assertCount(1, $subcomponents);
        $data = $subcomponents->getComponent(0)->getData();
        $this->assertInstanceOf(Primitive::class, $data);
        $this->assertSame('c', $data->getValue());

        // The subcomponent did not leak into the third component.
        $this->assertSame('', $hd->getUniversalIdType()->getValue());
    }
}
