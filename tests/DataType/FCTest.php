<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\FC;
use RoundingWell\HL7\Encoding;

#[CoversClass(FC::class)]
final class FCTest extends TestCase
{
    public function testComponentsMapClassCodeAndEffectiveDate(): void
    {
        // A financial class pairs its coded class with the date it takes effect.
        $fc = new FC();
        $fc->setRaw(new Encoding(), 'SELF^20260708');

        $this->assertSame('SELF', $fc->financialClassCode->identifier->getValue());
        $this->assertSame('20260708', $fc->effectiveDate->getValue());
    }
}
