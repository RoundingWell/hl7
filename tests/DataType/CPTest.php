<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\CP;
use RoundingWell\HL7\Encoding;

#[CoversClass(CP::class)]
final class CPTest extends TestCase
{
    public function testComponentsMapPriceRangeAndUnits(): void
    {
        // A composite price pairs a money amount with a range and its units.
        $cp = new CP();
        $cp->parse(new Encoding(), '100.00&USD^AP^0^1000^USD^F');

        $this->assertSame('100.00', $cp->getPrice()->getQuantity()->getValue());
        $this->assertSame('USD', $cp->getPrice()->getDenomination()->getValue());
        $this->assertSame('AP', $cp->getPriceType()->getValue());
        $this->assertSame('0', $cp->getFromValue()->getValue());
        $this->assertSame('1000', $cp->getToValue()->getValue());
        $this->assertSame('USD', $cp->getRangeUnits()->getIdentifier()->getValue());
        $this->assertSame('F', $cp->getRangeType()->getValue());
    }
}
