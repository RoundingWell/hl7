<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\Encoding;

#[CoversClass(CWE::class)]
final class CWETest extends TestCase
{
    public function testLeadingComponentsMapToIdentifierTextAndCodingSystem(): void
    {
        // Coded values are consumed by identifier/text/system, so their order must hold.
        $cwe = new CWE();
        $cwe->parse(new Encoding(), 'CODE^Display Name^SYS');

        $this->assertSame('CODE', $cwe->getIdentifier()->getValue());
        $this->assertSame('Display Name', $cwe->getText()->getValue());
        $this->assertSame('SYS', $cwe->getCodingSystem()->getValue());
    }

    public function testEveryComponentMapsToItsGetterInOrder(): void
    {
        // Distinct values per component confirm each getter reads its own position.
        $cwe = new CWE();
        $cwe->parse(new Encoding(), 'CODE^Display Name^SYS^ALT^Alternate Text^ALTSYS^V1^V2^Original Text');

        $this->assertSame('CODE', $cwe->getIdentifier()->getValue());
        $this->assertSame('Display Name', $cwe->getText()->getValue());
        $this->assertSame('SYS', $cwe->getCodingSystem()->getValue());
        $this->assertSame('ALT', $cwe->getAlternateIdentifier()->getValue());
        $this->assertSame('Alternate Text', $cwe->getAlternateText()->getValue());
        $this->assertSame('ALTSYS', $cwe->getAlternateCodingSystem()->getValue());
        $this->assertSame('V1', $cwe->getCodingSystemVersion()->getValue());
        $this->assertSame('V2', $cwe->getAlternateCodingSystemVersion()->getValue());
        $this->assertSame('Original Text', $cwe->getOriginalText()->getValue());
    }
}
