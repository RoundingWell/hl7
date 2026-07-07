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
        $cwe->setRaw(new Encoding(), 'CODE^Display Name^SYS');

        $this->assertSame('CODE', $cwe->identifier->getValue());
        $this->assertSame('Display Name', $cwe->text->getValue());
        $this->assertSame('SYS', $cwe->codingSystem->getValue());
    }
}
