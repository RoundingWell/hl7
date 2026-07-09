<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\TS;
use RoundingWell\HL7\Encoding;

#[CoversClass(TS::class)]
final class TSTest extends TestCase
{
    public function testComponentsMapToTimeAndPrecision(): void
    {
        // A timestamp pairs the instant with the precision to which it was recorded.
        $ts = new TS();
        $ts->parse(new Encoding(), '20240101^L');

        $this->assertSame('20240101', $ts->getTime()->getValue());
        $this->assertSame('L', $ts->getPrecision()->getValue());
    }
}
