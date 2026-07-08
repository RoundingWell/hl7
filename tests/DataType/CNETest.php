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
        $cne->setRaw(
            new Encoding(),
            'ID1^Text1^SYS1^ID2^Text2^SYS2^Ver1^Ver2^Orig^ID3^Text3^SYS3^Ver3^OID1^VSOID1^2024^OID2^VSOID2^2025^OID3^VSOID3^2026',
        );

        $this->assertSame('ID1', $cne->identifier->getValue());
        $this->assertSame('Text1', $cne->text->getValue());
        $this->assertSame('SYS1', $cne->codingSystem->getValue());
        $this->assertSame('ID2', $cne->alternateIdentifier->getValue());
        $this->assertSame('Text2', $cne->alternateText->getValue());
        $this->assertSame('SYS2', $cne->alternateCodingSystem->getValue());
        $this->assertSame('Ver1', $cne->codingSystemVersion->getValue());
        $this->assertSame('Ver2', $cne->alternateCodingSystemVersion->getValue());
        $this->assertSame('Orig', $cne->originalText->getValue());
        $this->assertSame('ID3', $cne->secondAlternateIdentifier->getValue());
        $this->assertSame('Text3', $cne->secondAlternateText->getValue());
        $this->assertSame('SYS3', $cne->secondAlternateCodingSystem->getValue());
        $this->assertSame('Ver3', $cne->secondAlternateCodingSystemVersion->getValue());
        $this->assertSame('OID1', $cne->codingSystemOid->getValue());
        $this->assertSame('VSOID1', $cne->valueSetOid->getValue());
        $this->assertSame('2024', $cne->valueSetVersion->getValue());
        $this->assertSame('OID2', $cne->alternateCodingSystemOid->getValue());
        $this->assertSame('VSOID2', $cne->alternateValueSetOid->getValue());
        $this->assertSame('2025', $cne->alternateValueSetVersion->getValue());
        $this->assertSame('OID3', $cne->secondAlternateCodingSystemOid->getValue());
        $this->assertSame('VSOID3', $cne->secondAlternateValueSetOid->getValue());
        $this->assertSame('2026', $cne->secondAlternateValueSetVersion->getValue());
    }
}
