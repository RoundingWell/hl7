<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\CE;
use RoundingWell\HL7\Encoding;

#[CoversClass(CE::class)]
final class CETest extends TestCase
{
    public function testLeadingComponentsMapToIdentifierTextAndCodingSystem(): void
    {
        // Coded values are consumed by identifier/text/system, so their order must hold.
        $ce = new CE();
        $ce->parse(new Encoding(), 'CODE^Display Name^SYS');

        $this->assertSame('CODE', $ce->getIdentifier()->getValue());
        $this->assertSame('Display Name', $ce->getText()->getValue());
        $this->assertSame('SYS', $ce->getCodingSystem()->getValue());
    }

    public function testEveryComponentMapsToItsGetterInOrder(): void
    {
        // Distinct values per component confirm each getter reads its own position.
        $ce = new CE();
        $ce->parse(new Encoding(), 'CODE^Display Name^SYS^ALT^Alternate Text^ALTSYS');

        $this->assertSame('CODE', $ce->getIdentifier()->getValue());
        $this->assertSame('Display Name', $ce->getText()->getValue());
        $this->assertSame('SYS', $ce->getCodingSystem()->getValue());
        $this->assertSame('ALT', $ce->getAlternateIdentifier()->getValue());
        $this->assertSame('Alternate Text', $ce->getAlternateText()->getValue());
        $this->assertSame('ALTSYS', $ce->getAlternateCodingSystem()->getValue());
    }
}
