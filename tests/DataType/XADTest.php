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
        // The leading component is itself a (sub-componentized) street address, followed by locality parts.
        $xad = new XAD();
        $xad->setRaw(new Encoding(), '111 DUCK ST^^FOWL^CA^999990000^USA^M');

        $this->assertSame('111 DUCK ST', $xad->streetAddress->streetAddress->getValue());
        $this->assertSame('FOWL', $xad->city->getValue());
        $this->assertSame('CA', $xad->stateOrProvince->getValue());
        $this->assertSame('999990000', $xad->zipOrPostalCode->getValue());
        $this->assertSame('USA', $xad->country->getValue());
    }
}
