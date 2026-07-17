<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\DT;
use RoundingWell\HL7\Exception\InvalidDateTime;

#[CoversClass(DT::class)]
final class DTTest extends TestCase
{
    public function testEmptyValueClearsTheDate(): void
    {
        // An absent date must report no value and no parsed date.
        $dt = new DT();
        $dt->setValue('');

        $this->assertSame('', $dt->getValue());
        $this->assertNull($dt->getDateTime());

        // Clearing the value must also discard the derived format.
        $this->assertNull($dt->getFormat());
    }

    public function testYearOnlyValueParsesWithoutOptionalComponents(): void
    {
        // The smallest legal precision (year) must parse without any month/day components,
        // and the absent components must be zeroed rather than filled from "now".
        $dt = new DT();
        $dt->setValue('2024');

        $this->assertSame('2024', $dt->getValue());
        $this->assertSame('2024-01-01', $dt->getDateTime()?->format('Y-m-d'));

        // Year-only precision must derive a year-only format, with ! forcing zeroed components.
        $this->assertSame('!Y', $dt->getFormat());
    }

    public function testFullValueParsesEveryOptionalComponent(): void
    {
        // Month and day must both be honored when present.
        $dt = new DT();
        $dt->setValue('20240315');

        $this->assertSame('20240315', $dt->getValue());
        $this->assertSame('2024-03-15', $dt->getDateTime()?->format('Y-m-d'));

        // Full precision must derive a format covering year, month, and day.
        $this->assertSame('!Ymd', $dt->getFormat());
    }

    public function testInvalidValueIsRejected(): void
    {
        $dt = new DT();

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dt->setValue('not-a-date');
    }
}
