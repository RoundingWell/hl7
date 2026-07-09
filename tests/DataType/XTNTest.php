<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\XTN;
use RoundingWell\HL7\Encoding;

#[CoversClass(XTN::class)]
final class XTNTest extends TestCase
{
    public function testComponentsMapPhoneNumberAndNumericParts(): void
    {
        // Every component is distinct and non-empty so each getter maps to its own value.
        // Components 15 and 16 are CWE composites and component 17 is an EI composite, so those
        // are asserted through a leaf getter. Dates use valid DTM timestamps.
        $xtn = new XTN();
        $xtn->parse(
            new Encoding(),
            '8885551212^PRN^PH^john@example.com^1^888^5551212^42^TEXT^x^SD^18885551212'
            . '^20200101^20301231^EXP^PROT^SHARE1^2',
        );

        $this->assertSame('8885551212', $xtn->getTelephoneNumber()->getValue());
        $this->assertSame('PRN', $xtn->getTelecommunicationUseCode()->getValue());
        $this->assertSame('PH', $xtn->getTelecommunicationEquipmentType()->getValue());
        $this->assertSame('john@example.com', $xtn->getCommunicationAddress()->getValue());
        $this->assertSame('1', $xtn->getCountryCode()->getValue());
        $this->assertSame('888', $xtn->getAreaCityCode()->getValue());
        $this->assertSame('5551212', $xtn->getLocalNumber()->getValue());
        $this->assertSame('42', $xtn->getExtension()->getValue());
        $this->assertSame('TEXT', $xtn->getAnyText()->getValue());
        $this->assertSame('x', $xtn->getExtensionPrefix()->getValue());
        $this->assertSame('SD', $xtn->getSpeedDialCode()->getValue());
        $this->assertSame('18885551212', $xtn->getUnformattedTelephoneNumber()->getValue());
        $this->assertSame('20200101', $xtn->getEffectiveStartDate()->getValue());
        $this->assertSame('20301231', $xtn->getExpirationDate()->getValue());
        $this->assertSame('EXP', $xtn->getExpirationReason()->getIdentifier()->getValue());
        $this->assertSame('PROT', $xtn->getProtectionCode()->getIdentifier()->getValue());
        $this->assertSame('SHARE1', $xtn->getSharedTelecommunicationIdentifier()->getId()->getValue());
        $this->assertSame('2', $xtn->getPreferenceOrder()->getValue());
    }
}
