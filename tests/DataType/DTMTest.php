<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\Exception\InvalidDateTime;

#[CoversClass(DTM::class)]
final class DTMTest extends TestCase
{
    public function testEmptyValueClearsTheDateTime(): void
    {
        // An absent timestamp must report no value and no parsed date.
        $dtm = new DTM();
        $dtm->setValue('');

        $this->assertSame('', $dtm->getValue());
        $this->assertNull($dtm->getDateTime());

        // Clearing the value must also discard the derived format.
        $this->assertNull($dtm->getFormat());
    }

    public function testYearOnlyValueParsesWithoutOptionalComponents(): void
    {
        // The smallest legal precision (year) must parse without any month/time components,
        // and the absent components must be zeroed rather than filled from "now".
        $dtm = new DTM();
        $dtm->setValue('2024');

        $this->assertSame('2024', $dtm->getValue());
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

    public function testSetValueDefersValidationAndNeverThrows(): void
    {
        // Parsing a message must not abort on a malformed date field: setValue stores the raw
        // value verbatim and defers all detection, so a downstream consumer can read the raw
        // value and validate it however it sees fit.
        $dtm = new DTM();
        $dtm->setValue('not-a-date');

        $this->assertSame('not-a-date', $dtm->getValue());
    }

    public function testGetFormatThrowsWhenCharacterPatternDoesNotMatch(): void
    {
        // getFormat runs the deferred character match; a value that cannot match the pattern
        // has no derivable format and must be rejected here.
        $dtm = new DTM();
        $dtm->setValue('not-a-date');

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dtm->getFormat();
    }

    public function testGetDateTimeThrowsWhenCharacterPatternDoesNotMatch(): void
    {
        // getDateTime resolves the format first, so a pattern mismatch surfaces here too.
        $dtm = new DTM();
        $dtm->setValue('not-a-date');

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dtm->getDateTime();
    }

    public function testClearDiscardsPreviouslyDetectedFormat(): void
    {
        // Detection is cached on first read; clearing the value must invalidate that cache so a
        // stale format derived from the old value is never reported for the now-empty primitive.
        $dtm = new DTM();
        $dtm->setValue('20240315123045');
        $this->assertSame('!YmdHis', $dtm->getFormat()); // prime the cached detection

        $dtm->clear();

        $this->assertSame('', $dtm->getValue());
        $this->assertNull($dtm->getFormat());
        $this->assertNull($dtm->getDateTime());
    }

    public function testGetFormatAcceptsPatternMatchThatCannotBuildAnInstant(): void
    {
        // The character pattern is deliberately permissive: it allows a trailing UTC offset
        // even when the intervening time components are absent (e.g. a year with an offset but
        // no month/day/time).
        // getFormat only validates the character pattern, so it must succeed here and return
        // that format — the impossibility of the instant is left for getDateTime to detect.
        $dtm = new DTM();
        $dtm->setValue('2024+0500');

        $this->assertSame('!YO', $dtm->getFormat());
    }

    public function testGetDateTimeRejectsPatternMatchThatCannotBuildAnInstant(): void
    {
        // A date with an invalid month/day/time is not a real instant and cannot build a
        // date. This exercises the createFromFormat-failure branch, distinct from a pattern
        // mismatch, and must be rejected by getDateTime.
        $dtm = new DTM();
        $dtm->setValue('20241301');

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dtm->getDateTime();
    }

    public function testSetValueParsesOffsetWithoutFractionalSeconds(): void
    {
        // An offset-bearing timestamp without fractional seconds is a legal HL7 DTM and must
        // parse (regression guard: setValue previously threw InvalidDateTime here due to an
        // isset/empty-string bug when the offset was present but fractional seconds were not).
        $dtm = new DTM();
        $dtm->setValue('20260717120000+0000');

        $this->assertSame('20260717120000+0000', $dtm->getValue());
        $this->assertSame('2026-07-17 12:00:00 +00:00', $dtm->getDateTime()?->format('Y-m-d H:i:s P'));
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
        $dtm = new DTM();
        $dtm->setValue($value);

        // getFormat validates only the character pattern, which these values satisfy, so it
        // must not throw — the out-of-range component is a construction failure, not a match
        // failure.
        $this->assertIsString($dtm->getFormat());

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dtm->getDateTime();
    }

    public function testSetDateTimeFormatsToHl7TimestampWithOffset(): void
    {
        // MSH-7 is populated from an injected clock; setDateTime must render an HL7 DTM
        // (YYYYMMDDHHMMSS±ZZZZ) and round-trip back through the parser to a consistent instant.
        $dtm = new DTM();
        $dtm->setDateTime(new DateTimeImmutable('2026-07-17 12:00:00', new DateTimeZone('+00:00')));

        $this->assertSame('20260717120000+0000', $dtm->getValue());
        $this->assertSame('2026-07-17 12:00:00 +00:00', $dtm->getDateTime()?->format('Y-m-d H:i:s P'));
    }
}
