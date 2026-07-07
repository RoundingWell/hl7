<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\DR;
use RoundingWell\HL7\Encoding;

#[CoversClass(DR::class)]
final class DRTest extends TestCase
{
    public function testComponentsMapToStartAndEndTimestamps(): void
    {
        // A date range must keep start and end distinct and in order.
        $dr = new DR();
        $dr->setRaw(new Encoding(), '20240101^20241231');

        $this->assertSame('20240101', $dr->start->time->getValue());
        $this->assertSame('20241231', $dr->end->time->getValue());
    }
}
