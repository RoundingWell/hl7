<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\XCN;
use RoundingWell\HL7\Encoding;

#[CoversClass(XCN::class)]
final class XCNTest extends TestCase
{
    public function testComponentsMapIdAndNestedFamilyName(): void
    {
        // A person reference pairs an id with a (sub-componentized) family name.
        $xcn = new XCN();
        $xcn->parse(new Encoding(), '37^DISNEY^WALT');

        $this->assertSame('37', $xcn->getId()->getValue());
        $this->assertSame('DISNEY', $xcn->getFamilyName()->getSurname()->getValue());
        $this->assertSame('WALT', $xcn->getGivenName()->getValue());
    }

    public function testEveryComponentMapsToItsGetter(): void
    {
        // A fully-populated value proves each getter reads the correct positional
        // component; distinct values guarantee an off-by-one mapping would fail.
        // Composite components (family name, authorities, contexts, ranges, timestamps)
        // are asserted through a leaf so the nested wiring is exercised too, and the
        // timestamp leaves use valid HL7 dates because DTM validates its input.
        $xcn = new XCN();
        $xcn->parse(
            new Encoding(),
            '37^DISNEY^WALT^E^JR^MR^MD^TBL^AUTH^L^9^M10^MRN^FAC^A^CTX^2019^F^2020^2021^PHD^JUR^AGY',
        );

        $this->assertSame('37', $xcn->getId()->getValue());
        $this->assertSame('DISNEY', $xcn->getFamilyName()->getSurname()->getValue());
        $this->assertSame('WALT', $xcn->getGivenName()->getValue());
        $this->assertSame('E', $xcn->getFurtherGivenNames()->getValue());
        $this->assertSame('JR', $xcn->getSuffix()->getValue());
        $this->assertSame('MR', $xcn->getPrefix()->getValue());
        $this->assertSame('MD', $xcn->getDegree()->getValue());
        $this->assertSame('TBL', $xcn->getSourceTable()->getValue());
        $this->assertSame('AUTH', $xcn->getAssigningAuthority()->getNamespaceId()->getValue());
        $this->assertSame('L', $xcn->getNameTypeCode()->getValue());
        $this->assertSame('9', $xcn->getIdentifierCheckDigit()->getValue());
        $this->assertSame('M10', $xcn->getCheckDigitScheme()->getValue());
        $this->assertSame('MRN', $xcn->getIdentifierTypeCode()->getValue());
        $this->assertSame('FAC', $xcn->getAssigningFacility()->getNamespaceId()->getValue());
        $this->assertSame('A', $xcn->getNameRepresentationCode()->getValue());
        $this->assertSame('CTX', $xcn->getNameContext()->getIdentifier()->getValue());
        $this->assertSame('2019', $xcn->getNameValidityRange()->getStart()->getTime()->getValue());
        $this->assertSame('F', $xcn->getNameAssemblyOrder()->getValue());
        $this->assertSame('2020', $xcn->getEffectiveDate()->getTime()->getValue());
        $this->assertSame('2021', $xcn->getExpirationDate()->getTime()->getValue());
        $this->assertSame('PHD', $xcn->getProfessionalSuffix()->getValue());
        $this->assertSame('JUR', $xcn->getAssigningJurisdiction()->getIdentifier()->getValue());
        $this->assertSame('AGY', $xcn->getAssigningAgencyOrDepartment()->getIdentifier()->getValue());
    }
}
