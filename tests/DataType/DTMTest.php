<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidDateTime;

#[CoversClass(DTM::class)]
final class DTMTest extends TestCase
{
    public function testEmptyValueClearsTheDateTime(): void
    {
        // An absent timestamp must report no value and no parsed date.
        $dtm = new DTM();
        $dtm->setRaw(new Encoding(), '');

        $this->assertFalse($dtm->hasValue());
        $this->assertSame('', $dtm->getValue());
        $this->assertNull($dtm->getDateTime());
        $this->assertSame('', (string) $dtm);

        // Clearing the value must also discard the derived format.
        $this->assertNull($dtm->getFormat());
    }

    public function testYearOnlyValueParsesWithoutOptionalComponents(): void
    {
        // The smallest legal precision (year) must parse without any month/time components,
        // and the absent components must be zeroed rather than filled from "now".
        $dtm = new DTM();
        $dtm->setValue('2024');

        $this->assertTrue($dtm->hasValue());
        $this->assertSame('2024', $dtm->getValue());
        $this->assertSame('2024', (string) $dtm);
        $this->assertSame('2024-01-01 00:00:00', $dtm->getDateTime()?->format('Y-m-d H:i:s'));

        // Year-only precision must derive a year-only format, with ! forcing zeroed components.
        $this->assertSame('!Y', $dtm->getFormat());
    }

    public function testFullValueParsesEveryOptionalComponent(): void
    {
        // Date, time, fractional seconds, and offset must all be honored when present.
        $dtm = new DTM();
        $dtm->setValue('20240315123045.1234+0500');

        $this->assertSame('20240315123045.1234+0500', $dtm->getValue());
        $this->assertSame('2024-03-15 12:30:45 +05:00', $dtm->getDateTime()?->format('Y-m-d H:i:s P'));

        // Full precision must derive a format covering every optional component.
        $this->assertSame('!YmdHis.uO', $dtm->getFormat());
    }

    public function testInvalidValueIsRejected(): void
    {
        $dtm = new DTM();

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessageIsOrContains('HL7 expected date/time');

        $dtm->setValue('not-a-date');
    }
}
