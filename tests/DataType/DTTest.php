<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\DT;
use RoundingWell\HL7\Exception\InvalidDate;

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

    public function testSetValueDefersValidationAndNeverThrows(): void
    {
        // Parsing a message must not abort on a malformed date field: setValue stores the raw
        // value verbatim and defers all detection, so a downstream consumer can read the raw
        // value and validate it however it sees fit.
        $dt = new DT();
        $dt->setValue('not-a-date');

        $this->assertSame('not-a-date', $dt->getValue());
    }

    public function testGetFormatThrowsWhenCharacterPatternDoesNotMatch(): void
    {
        // getFormat runs the deferred character match; a value that cannot match the pattern
        // has no derivable format and must be rejected here.
        $dt = new DT();
        $dt->setValue('not-a-date');

        $this->expectException(InvalidDate::class);
        $this->expectExceptionMessage('HL7 expected date');

        $dt->getFormat();
    }

    public function testGetDateTimeThrowsWhenCharacterPatternDoesNotMatch(): void
    {
        // getDateTime resolves the format first, so a pattern mismatch surfaces here too.
        $dt = new DT();
        $dt->setValue('not-a-date');

        $this->expectException(InvalidDate::class);
        $this->expectExceptionMessage('HL7 expected date');

        $dt->getDateTime();
    }

    public function testClearDiscardsPreviouslyDetectedFormat(): void
    {
        // Detection is cached on first read; clearing the value must invalidate that cache so a
        // stale format derived from the old value is never reported for the now-empty primitive.
        $dt = new DT();
        $dt->setValue('20240315');
        $this->assertSame('!Ymd', $dt->getFormat()); // prime the cached detection

        $dt->clear();

        $this->assertSame('', $dt->getValue());
        $this->assertNull($dt->getFormat());
        $this->assertNull($dt->getDateTime());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function outOfRangeComponents(): array
    {
        // Each value matches the character pattern but carries a component outside its
        // calendar range. PHP's createFromFormat silently rolls these over (e.g. month 13
        // becomes January of the next year) instead of failing, so an unguarded parse would
        // accept a value that means something other than what was written. These must be
        // rejected so a mis-typed date never masquerades as a valid, different day.
        return [
            'month 00' => ['20230015'],
            'month 13' => ['20231315'],
            'day 00' => ['20230300'],
            'day 32' => ['20230332'],
            'impossible calendar date (Feb 30)' => ['20230230'],
            'impossible calendar date (Apr 31)' => ['20230431'],
        ];
    }

    #[DataProvider('outOfRangeComponents')]
    public function testOutOfRangeComponentIsRejectedByGetDateTime(string $value): void
    {
        $dt = new DT();
        $dt->setValue($value);

        // getFormat validates only the character pattern, which these values satisfy, so it
        // must not throw — the out-of-range component is a construction failure, not a match
        // failure.
        $this->assertIsString($dt->getFormat());

        $this->expectException(InvalidDate::class);
        $this->expectExceptionMessage('HL7 expected date');

        $dt->getDateTime();
    }

    public function testSetDateFormatsToHl7DateAndDropsTime(): void
    {
        // setDate must render an HL7 date (YYYYMMDD), dropping any time and offset carried by
        // the source value, and round-trip back through the parser to the same calendar day.
        $dt = new DT();
        $dt->setDate(new DateTimeImmutable('2026-07-17 12:34:56', new DateTimeZone('+05:00')));

        $this->assertSame('20260717', $dt->getValue());
        $this->assertSame('2026-07-17', $dt->getDateTime()?->format('Y-m-d'));
        $this->assertSame('!Ymd', $dt->getFormat());
    }
}
