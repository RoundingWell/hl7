<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\DT;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidDateTime;

#[CoversClass(DT::class)]
final class DTTest extends TestCase
{
    public function testEmptyValueClearsTheDate(): void
    {
        // An absent date must report no value and no parsed date.
        $dt = new DT();
        $dt->setRaw(new Encoding(), '');

        $this->assertFalse($dt->hasValue());
        $this->assertSame('', $dt->getValue());
        $this->assertNull($dt->getDateTime());
        $this->assertSame('', (string) $dt);
    }

    public function testYearOnlyValueParsesWithoutOptionalComponents(): void
    {
        // The smallest legal precision (year) must parse without any month/day components.
        $dt = new DT();
        $dt->setValue('2024');

        $this->assertTrue($dt->hasValue());
        $this->assertSame('2024', $dt->getValue());
        $this->assertSame('2024', (string) $dt);
        $this->assertSame('2024', $dt->getDateTime()?->format('Y'));
    }

    public function testFullValueParsesEveryOptionalComponent(): void
    {
        // Month and day must both be honored when present.
        $dt = new DT();
        $dt->setValue('20240315');

        $this->assertSame('20240315', $dt->getValue());
        $this->assertSame('20240315', (string) $dt);
        $this->assertNotNull($dt->getDateTime());
        $this->assertSame('2024-03-15', $dt->getDateTime()?->format('Y-m-d'));
    }

    public function testInvalidValueIsRejected(): void
    {
        $dt = new DT();

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessageIsOrContains('HL7 expected date/time');

        $dt->setValue('not-a-date');
    }
}
