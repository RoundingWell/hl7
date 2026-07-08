<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\MO;
use RoundingWell\HL7\Encoding;

#[CoversClass(MO::class)]
final class MOTest extends TestCase
{
    public function testComponentsMapQuantityAndDenomination(): void
    {
        // Money pairs a numeric quantity with its currency denomination.
        $mo = new MO();
        $mo->setRaw(new Encoding(), '99.50^USD');

        $this->assertSame('99.50', $mo->quantity->getValue());
        $this->assertSame('USD', $mo->denomination->getValue());
    }
}
