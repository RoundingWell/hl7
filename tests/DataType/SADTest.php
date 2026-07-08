<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\SAD;
use RoundingWell\HL7\Encoding;

#[CoversClass(SAD::class)]
final class SADTest extends TestCase
{
    public function testComponentsMapToTheStreetAddressParts(): void
    {
        // Component position determines meaning within a street address.
        $sad = new SAD();
        $sad->setRaw(new Encoding(), '111 DUCK ST^Duck Street^7');

        $this->assertSame('111 DUCK ST', $sad->streetAddress->getValue());
        $this->assertSame('Duck Street', $sad->streetName->getValue());
        $this->assertSame('7', $sad->dwellingNumber->getValue());
    }
}
