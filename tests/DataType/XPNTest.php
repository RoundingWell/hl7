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
        $xpn->setRaw(new Encoding(), 'DUCK^DONALD^D^JR^MR');

        $this->assertSame('DUCK', $xpn->familyName->surname->getValue());
        $this->assertSame('DONALD', $xpn->givenName->getValue());
        $this->assertSame('D', $xpn->furtherGivenNames->getValue());
        $this->assertSame('JR', $xpn->suffix->getValue());
        $this->assertSame('MR', $xpn->prefix->getValue());
    }
}
