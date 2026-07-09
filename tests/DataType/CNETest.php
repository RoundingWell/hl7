<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\CNE;
use RoundingWell\HL7\Encoding;

#[CoversClass(CNE::class)]
final class CNETest extends TestCase
{
    public function testComponentsMapEveryCodedElement(): void
    {
        // A coded-no-exceptions element carries a code across primary and alternate coding systems.
        $cne = new CNE();
        $cne->parse(
            new Encoding(),
            'ID1^Text1^SYS1^ID2^Text2^SYS2^Ver1^Ver2^Orig^ID3^Text3^SYS3^Ver3^OID1^VSOID1^2024^OID2^VSOID2^2025^OID3^VSOID3^2026',
        );

        $this->assertSame('ID1', $cne->getIdentifier()->getValue());
        $this->assertSame('Text1', $cne->getText()->getValue());
        $this->assertSame('SYS1', $cne->getCodingSystem()->getValue());
        $this->assertSame('ID2', $cne->getAlternateIdentifier()->getValue());
        $this->assertSame('Text2', $cne->getAlternateText()->getValue());
        $this->assertSame('SYS2', $cne->getAlternateCodingSystem()->getValue());
        $this->assertSame('Ver1', $cne->getCodingSystemVersion()->getValue());
        $this->assertSame('Ver2', $cne->getAlternateCodingSystemVersion()->getValue());
        $this->assertSame('Orig', $cne->getOriginalText()->getValue());
        $this->assertSame('ID3', $cne->getSecondAlternateIdentifier()->getValue());
        $this->assertSame('Text3', $cne->getSecondAlternateText()->getValue());
        $this->assertSame('SYS3', $cne->getSecondAlternateCodingSystem()->getValue());
        $this->assertSame('Ver3', $cne->getSecondAlternateCodingSystemVersion()->getValue());
        $this->assertSame('OID1', $cne->getCodingSystemOid()->getValue());
        $this->assertSame('VSOID1', $cne->getValueSetOid()->getValue());
        $this->assertSame('2024', $cne->getValueSetVersion()->getValue());
        $this->assertSame('OID2', $cne->getAlternateCodingSystemOid()->getValue());
        $this->assertSame('VSOID2', $cne->getAlternateValueSetOid()->getValue());
        $this->assertSame('2025', $cne->getAlternateValueSetVersion()->getValue());
        $this->assertSame('OID3', $cne->getSecondAlternateCodingSystemOid()->getValue());
        $this->assertSame('VSOID3', $cne->getSecondAlternateValueSetOid()->getValue());
        $this->assertSame('2026', $cne->getSecondAlternateValueSetVersion()->getValue());
    }
}
