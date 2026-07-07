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
        $ce->setRaw(new Encoding(), '71596^OSTEOARTHROS^I9');

        $this->assertSame('71596', $ce->identifier->getValue());
        $this->assertSame('OSTEOARTHROS', $ce->text->getValue());
        $this->assertSame('I9', $ce->codingSystem->getValue());
    }
}
