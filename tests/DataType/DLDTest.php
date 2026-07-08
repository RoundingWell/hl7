<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\DLD;
use RoundingWell\HL7\Encoding;

#[CoversClass(DLD::class)]
final class DLDTest extends TestCase
{
    public function testComponentsMapDischargeLocationAndDate(): void
    {
        // A discharge disposition pairs the location a patient is sent to with the effective date.
        $dld = new DLD();
        $dld->setRaw(new Encoding(), 'SNF^20260708');

        $this->assertSame('SNF', $dld->dischargeToLocation->identifier->getValue());
        $this->assertSame('20260708', $dld->effectiveDate->getValue());
    }
}
