<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\XAD;
use RoundingWell\HL7\Encoding;

#[CoversClass(XAD::class)]
final class XADTest extends TestCase
{
    public function testComponentsMapNestedStreetAddressAndLocality(): void
    {
        // Every component is distinct and non-empty so each getter maps to its own value.
        // Component 1 (Street Address) is a sub-componentized SAD (joined with &); components
        // 9, 10, 15 and 22 are CWE composites and component 23 is an EI composite, so those are
        // asserted through a leaf getter. Dates use valid DTM timestamps.
        $xad = new XAD();
        $xad->parse(
            new Encoding(),
            '111 DUCK ST&MAIN ST&12^APT 2^FOWL^CA^999990000^USA^M^GEO^CTY^CEN^A^RANGE'
            . '^20200101^20301231^EXP^Y^N^H^JOHN DOE^COMMENT^1^PROT^ID123',
        );

        $this->assertSame('111 DUCK ST', $xad->getStreetAddress()->getStreetAddress()->getValue());
        $this->assertSame('APT 2', $xad->getOtherDesignation()->getValue());
        $this->assertSame('FOWL', $xad->getCity()->getValue());
        $this->assertSame('CA', $xad->getStateOrProvince()->getValue());
        $this->assertSame('999990000', $xad->getZipOrPostalCode()->getValue());
        $this->assertSame('USA', $xad->getCountry()->getValue());
        $this->assertSame('M', $xad->getAddressType()->getValue());
        $this->assertSame('GEO', $xad->getOtherGeographicDesignation()->getValue());
        $this->assertSame('CTY', $xad->getCountyParishCode()->getIdentifier()->getValue());
        $this->assertSame('CEN', $xad->getCensusTract()->getIdentifier()->getValue());
        $this->assertSame('A', $xad->getAddressRepresentationCode()->getValue());
        $this->assertSame('RANGE', $xad->getAddressValidityRange()->getValue());
        $this->assertSame('20200101', $xad->getEffectiveDate()->getValue());
        $this->assertSame('20301231', $xad->getExpirationDate()->getValue());
        $this->assertSame('EXP', $xad->getExpirationReason()->getIdentifier()->getValue());
        $this->assertSame('Y', $xad->getTemporaryIndicator()->getValue());
        $this->assertSame('N', $xad->getBadAddressIndicator()->getValue());
        $this->assertSame('H', $xad->getAddressUsage()->getValue());
        $this->assertSame('JOHN DOE', $xad->getAddressee()->getValue());
        $this->assertSame('COMMENT', $xad->getComment()->getValue());
        $this->assertSame('1', $xad->getPreferenceOrder()->getValue());
        $this->assertSame('PROT', $xad->getProtectionCode()->getIdentifier()->getValue());
        $this->assertSame('ID123', $xad->getAddressIdentifier()->getId()->getValue());
    }
}
