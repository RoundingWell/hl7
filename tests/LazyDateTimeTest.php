<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\InvalidDateTime;
use RoundingWell\HL7\LazyDateTime;

#[CoversClass(LazyDateTime::class)]
final class LazyDateTimeTest extends TestCase
{
    public function testYearOnlyValueDerivesYearOnlyFormatAndZeroesTheRest(): void
    {
        // The smallest legal precision (year) must parse without any month/time components.
        // The ! prefix must force the absent components to zero rather than inherit "now",
        // so a bare year always resolves to midnight on January 1st of that year.
        $dateTime = new LazyDateTime('2024');

        $this->assertSame('!Y', $dateTime->getFormat());
        $this->assertSame('2024-01-01 00:00:00', $dateTime->getDateTime()->format('Y-m-d H:i:s'));
    }

    public function testFullValueParsesEveryOptionalComponent(): void
    {
        // Date, time, fractional seconds, and offset must all be honored when present.
        $dateTime = new LazyDateTime('20240315123045.1234+0500');

        $this->assertSame('!YmdHis.uO', $dateTime->getFormat());
        $this->assertSame('2024-03-15 12:30:45 +05:00', $dateTime->getDateTime()->format('Y-m-d H:i:s P'));
    }

    public function testGetFormatThrowsWhenCharacterPatternDoesNotMatch(): void
    {
        // A value that cannot match the timestamp character pattern has no derivable format and
        // must be rejected the moment a format is requested.
        $dateTime = new LazyDateTime('not-a-date');

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dateTime->getFormat();
    }

    public function testGetDateTimeThrowsWhenCharacterPatternDoesNotMatch(): void
    {
        // getDateTime resolves the format first, so a pattern mismatch must surface here too
        // rather than reaching the instant construction step.
        $dateTime = new LazyDateTime('not-a-date');

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dateTime->getDateTime();
    }

    public function testYearWithOffsetDerivesYearAndOffsetFormatAndBuildsAValidInstant(): void
    {
        // The character pattern allows a trailing UTC offset directly after the year with no
        // intervening month/day/time. Only the components actually present enter the derived
        // format, so a year+offset is a real instant: midnight on January 1st at that offset,
        // with the absent components zeroed by the ! prefix rather than filled from "now".
        $dateTime = new LazyDateTime('2024+0500');

        $this->assertSame('!YO', $dateTime->getFormat());
        $this->assertSame('2024-01-01 00:00:00 +05:00', $dateTime->getDateTime()->format('Y-m-d H:i:s P'));
    }

    public function testGetDateTimeRejectsPatternMatchThatIsNotARealInstant(): void
    {
        // A value can satisfy the character pattern yet name an impossible instant (here
        // month 13). createFromFormat silently rolls that over, so getDateTime must reject it
        // rather than report a different, valid instant. This exercises the construction-failure
        // branch, distinct from a character-pattern mismatch.
        $dateTime = new LazyDateTime('20241301');

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dateTime->getDateTime();
    }

    public function testGetDateTimeResolvesItsOwnFormatWithoutAPriorGetFormatCall(): void
    {
        // getDateTime must be self-contained: callers reach for the instant directly without
        // first priming the format. Resolving the format internally is what makes that safe
        // (regression guard: getDateTime previously read an unresolved null format and raised
        // a TypeError instead of building the instant).
        $dateTime = new LazyDateTime('20240315123045');

        $this->assertSame('2024-03-15 12:30:45', $dateTime->getDateTime()->format('Y-m-d H:i:s'));
    }

    public function testParsesOffsetWithoutFractionalSeconds(): void
    {
        // An offset-bearing timestamp without fractional seconds is a legal HL7 timestamp and
        // must parse (regression guard: the optional fractional-seconds group being empty must
        // not prevent the offset group from being honored).
        $dateTime = new LazyDateTime('20260717120000+0000');

        $this->assertSame('!YmdHisO', $dateTime->getFormat());
        $this->assertSame('2026-07-17 12:00:00 +00:00', $dateTime->getDateTime()->format('Y-m-d H:i:s P'));
    }

    public function testRepeatedReadsAreMemoized(): void
    {
        // Detection and construction are cached on first read so a value is parsed once; a
        // second read must return the identical cached result rather than re-deriving it.
        $dateTime = new LazyDateTime('20240315123045');

        $format = $dateTime->getFormat();
        $this->assertSame($format, $dateTime->getFormat());

        $instant = $dateTime->getDateTime();
        $this->assertSame($instant, $dateTime->getDateTime());
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
        // rejected so a mis-typed timestamp never masquerades as a valid, different instant.
        return [
            'month 00' => ['20230015'],
            'month 13' => ['20231315'],
            'day 00' => ['20230300'],
            'day 32' => ['20230332'],
            'impossible calendar date (Feb 30)' => ['20230230'],
            'impossible calendar date (Apr 31)' => ['20230431'],
            'hour 24' => ['2023031524'],
            'minute 60' => ['202303151260'],
            'second 60' => ['20230315123060'],
        ];
    }

    #[DataProvider('outOfRangeComponents')]
    public function testOutOfRangeComponentIsRejectedByGetDateTime(string $value): void
    {
        $dateTime = new LazyDateTime($value);

        // getFormat validates only the character pattern, which these values satisfy, so it
        // must not throw — the out-of-range component is a construction failure, not a match
        // failure.
        $this->assertIsString($dateTime->getFormat());

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dateTime->getDateTime();
    }
}
