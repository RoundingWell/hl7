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
        $pl->setRaw(new Encoding(), 'POC^ROOM^BED^FAC^A^E^BLDG^FLR^Main Ward^EI1^AUTH');

        $this->assertSame('POC', $pl->pointOfCare->namespaceId->getValue());
        $this->assertSame('ROOM', $pl->room->namespaceId->getValue());
        $this->assertSame('BED', $pl->bed->namespaceId->getValue());
        $this->assertSame('FAC', $pl->facility->namespaceId->getValue());
        $this->assertSame('A', $pl->locationStatus->getValue());
        $this->assertSame('E', $pl->personLocationType->getValue());
        $this->assertSame('BLDG', $pl->building->namespaceId->getValue());
        $this->assertSame('FLR', $pl->floor->namespaceId->getValue());
        $this->assertSame('Main Ward', $pl->locationDescription->getValue());
        $this->assertSame('EI1', $pl->comprehensiveLocationIdentifier->id->getValue());
        $this->assertSame('AUTH', $pl->assigningAuthorityForLocation->namespaceId->getValue());
    }
}
