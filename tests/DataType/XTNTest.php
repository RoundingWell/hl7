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
        // The telephone number leads, followed by use/equipment codes and the numeric dialing parts.
        $xtn = new XTN();
        $xtn->setRaw(new Encoding(), '8885551212^PRN^PH^^1^888^5551212');

        $this->assertSame('8885551212', $xtn->telephoneNumber->getValue());
        $this->assertSame('PRN', $xtn->telecommunicationUseCode->getValue());
        $this->assertSame('1', $xtn->countryCode->getValue());
        $this->assertSame('888', $xtn->areaCityCode->getValue());
        $this->assertSame('5551212', $xtn->localNumber->getValue());
    }
}
