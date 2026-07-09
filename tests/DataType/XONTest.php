<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\XON;
use RoundingWell\HL7\Encoding;

#[CoversClass(XON::class)]
final class XONTest extends TestCase
{
    public function testFirstComponentMapsToTheOrganizationName(): void
    {
        // The organization name is the primary component of an organization reference.
        $xon = new XON();
        $xon->parse(new Encoding(), 'Cartoon Ducks Inc');

        $this->assertSame('Cartoon Ducks Inc', $xon->getOrganizationName()->getValue());
    }

    public function testEveryComponentMapsToItsGetter(): void
    {
        // The assigning authority and facility are HD composites carried as
        // subcomponents, so they must split on '&' into their own components.
        $xon = new XON();
        $xon->parse(new Encoding(), 'Cartoon Ducks Inc^L^12345^7^M10^Auth&1.2.840&ISO^NPI^Fac&2.16.840&ISO^A^ORG9');

        $this->assertSame('Cartoon Ducks Inc', $xon->getOrganizationName()->getValue());
        $this->assertSame('L', $xon->getNameTypeCode()->getIdentifier()->getValue());
        $this->assertSame('12345', $xon->getIdNumber()->getValue());
        $this->assertSame('7', $xon->getIdentifierCheckDigit()->getValue());
        $this->assertSame('M10', $xon->getCheckDigitScheme()->getValue());
        $this->assertSame('Auth', $xon->getAssigningAuthority()->getNamespaceId()->getValue());
        $this->assertSame('NPI', $xon->getIdentifierTypeCode()->getValue());
        $this->assertSame('Fac', $xon->getAssigningFacility()->getNamespaceId()->getValue());
        $this->assertSame('A', $xon->getNameRepresentationCode()->getValue());
        $this->assertSame('ORG9', $xon->getOrganizationIdentifier()->getValue());
    }
}
