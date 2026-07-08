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
        // The nested money (MO) component is itself delimited by subcomponents.
        $cp = new CP();
        $cp->setRaw(new Encoding(), '100.00&USD^AP^0^1000^USD^F');

        $this->assertSame('100.00', $cp->price->quantity->getValue());
        $this->assertSame('USD', $cp->price->denomination->getValue());
        $this->assertSame('AP', $cp->priceType->getValue());
        $this->assertSame('0', $cp->fromValue->getValue());
        $this->assertSame('1000', $cp->toValue->getValue());
        $this->assertSame('USD', $cp->rangeUnits->identifier->getValue());
        $this->assertSame('F', $cp->rangeType->getValue());
    }
}
