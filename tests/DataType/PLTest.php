<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\PL;
use RoundingWell\HL7\Encoding;

#[CoversClass(PL::class)]
final class PLTest extends TestCase
{
    public function testComponentsMapEveryLocationField(): void
    {
        // A person location composes care setting, room, bed, and facility identifiers.
        $pl = new PL();
        $pl->parse(new Encoding(), 'POC^ROOM^BED^FAC^A^E^BLDG^FLR^Main Ward^EI1^AUTH');

        $this->assertSame('POC', $pl->getPointOfCare()->getNamespaceId()->getValue());
        $this->assertSame('ROOM', $pl->getRoom()->getNamespaceId()->getValue());
        $this->assertSame('BED', $pl->getBed()->getNamespaceId()->getValue());
        $this->assertSame('FAC', $pl->getFacility()->getNamespaceId()->getValue());
        $this->assertSame('A', $pl->getLocationStatus()->getValue());
        $this->assertSame('E', $pl->getPersonLocationType()->getValue());
        $this->assertSame('BLDG', $pl->getBuilding()->getNamespaceId()->getValue());
        $this->assertSame('FLR', $pl->getFloor()->getNamespaceId()->getValue());
        $this->assertSame('Main Ward', $pl->getLocationDescription()->getValue());
        $this->assertSame('EI1', $pl->getComprehensiveLocationIdentifier()->getId()->getValue());
        $this->assertSame('AUTH', $pl->getAssigningAuthorityForLocation()->getNamespaceId()->getValue());
    }
}
