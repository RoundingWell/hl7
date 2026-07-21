<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\InvalidDate;
use RoundingWell\HL7\LazyDate;

#[CoversClass(LazyDate::class)]
final class LazyDateTest extends TestCase
{
    public function testYearOnlyValueDerivesYearOnlyFormatAndZeroesTheRest(): void
    {
        // The smallest legal precision (year) must parse without any month/day components.
        // The ! prefix must force the absent components to zero rather than inherit "now",
        // so a bare year always resolves to midnight on January 1st of that year.
        $date = new LazyDate('2024');

        $this->assertSame('!Y', $date->getFormat());
        $this->assertSame('2024-01-01 00:00:00', $date->getDateTime()->format('Y-m-d H:i:s'));
    }

    public function testMonthPrecisionValueDerivesPartialFormat(): void
    {
        // A year-month value carries the optional month but not the day, so the derived format
        // must include m but stop before d — the day must still be zeroed to the 1st.
        $date = new LazyDate('202403');

        $this->assertSame('!Ym', $date->getFormat());
        $this->assertSame('2024-03-01', $date->getDateTime()->format('Y-m-d'));
    }

    public function testFullValueParsesEveryOptionalComponent(): void
    {
        // Month and day must both be honored when present.
        $date = new LazyDate('20240315');

        $this->assertSame('!Ymd', $date->getFormat());
        $this->assertSame('2024-03-15', $date->getDateTime()->format('Y-m-d'));
    }

    public function testGetFormatThrowsWhenCharacterPatternDoesNotMatch(): void
    {
        // A value that cannot match the YYYY[MM[DD]] character pattern has no derivable format
        // and must be rejected the moment a format is requested.
        $date = new LazyDate('not-a-date');

        $this->expectException(InvalidDate::class);
        $this->expectExceptionMessage('HL7 expected date');

        $date->getFormat();
    }

    public function testGetDateTimeThrowsWhenCharacterPatternDoesNotMatch(): void
    {
        // getDateTime resolves the format first, so a pattern mismatch must surface here too
        // rather than reaching the date construction step.
        $date = new LazyDate('not-a-date');

        $this->expectException(InvalidDate::class);
        $this->expectExceptionMessage('HL7 expected date');

        $date->getDateTime();
    }

    public function testGetDateTimeResolvesItsOwnFormatWithoutAPriorGetFormatCall(): void
    {
        // getDateTime must be self-contained: callers reach for the instant directly without
        // first priming the format. Resolving the format internally is what makes that safe
        // (regression guard: getDateTime previously read an unresolved null format and raised
        // a TypeError instead of building the date).
        $date = new LazyDate('20240315');

        $this->assertSame('2024-03-15', $date->getDateTime()->format('Y-m-d'));
    }

    public function testRepeatedReadsAreMemoized(): void
    {
        // Detection and construction are cached on first read so a value is parsed once; a
        // second read must return the identical cached result rather than re-deriving it.
        $date = new LazyDate('20240315');

        $format = $date->getFormat();
        $this->assertSame($format, $date->getFormat());

        $dateTime = $date->getDateTime();
        $this->assertSame($dateTime, $date->getDateTime());
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
        $date = new LazyDate($value);

        // getFormat validates only the character pattern, which these values satisfy, so it
        // must not throw — the out-of-range component is a construction failure, not a match
        // failure.
        $this->assertIsString($date->getFormat());

        $this->expectException(InvalidDate::class);
        $this->expectExceptionMessage('HL7 expected date');

        $date->getDateTime();
    }
}
