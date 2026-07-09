<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\XPN;
use RoundingWell\HL7\Encoding;

#[CoversClass(XPN::class)]
final class XPNTest extends TestCase
{
    public function testComponentsMapNestedFamilyNameAndGivenNames(): void
    {
        // The leading component is a (sub-componentized) family name, followed by the given names.
        $xpn = new XPN();
        $xpn->parse(new Encoding(), 'DUCK^DONALD^D^JR^MR');

        $this->assertSame('DUCK', $xpn->getFamilyName()->getSurname()->getValue());
        $this->assertSame('DONALD', $xpn->getGivenName()->getValue());
        $this->assertSame('D', $xpn->getFurtherGivenNames()->getValue());
        $this->assertSame('JR', $xpn->getSuffix()->getValue());
        $this->assertSame('MR', $xpn->getPrefix()->getValue());
    }

    public function testEveryComponentMapsToItsGetter(): void
    {
        // A fully-populated value proves each getter reads the correct positional
        // component; distinct values guarantee an off-by-one mapping would fail.
        // The family name and name context are composites asserted via a leaf, and the
        // effective/expiration dates use valid HL7 timestamps because DTM validates input.
        $xpn = new XPN();
        $xpn->parse(new Encoding(), 'DUCK^DONALD^D^JR^MR^MD^L^A^CTX^RANGE^F^2020^2021^PHD^DON');

        $this->assertSame('DUCK', $xpn->getFamilyName()->getSurname()->getValue());
        $this->assertSame('DONALD', $xpn->getGivenName()->getValue());
        $this->assertSame('D', $xpn->getFurtherGivenNames()->getValue());
        $this->assertSame('JR', $xpn->getSuffix()->getValue());
        $this->assertSame('MR', $xpn->getPrefix()->getValue());
        $this->assertSame('MD', $xpn->getDegree()->getValue());
        $this->assertSame('L', $xpn->getNameTypeCode()->getValue());
        $this->assertSame('A', $xpn->getNameRepresentationCode()->getValue());
        $this->assertSame('CTX', $xpn->getNameContext()->getIdentifier()->getValue());
        $this->assertSame('RANGE', $xpn->getNameValidityRange()->getValue());
        $this->assertSame('F', $xpn->getNameAssemblyOrder()->getValue());
        $this->assertSame('2020', $xpn->getEffectiveDate()->getValue());
        $this->assertSame('2021', $xpn->getExpirationDate()->getValue());
        $this->assertSame('PHD', $xpn->getProfessionalSuffix()->getValue());
        $this->assertSame('DON', $xpn->getCalledBy()->getValue());
    }
}
