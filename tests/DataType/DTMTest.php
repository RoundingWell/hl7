<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

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

    public function testInvalidValueIsRejected(): void
    {
        $dtm = new DTM();

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dtm->setValue('not-a-date');
    }

    public function testPatternMatchButUnbuildableTimestampIsRejected(): void
    {
        // The character pattern is deliberately permissive: it allows a trailing UTC offset
        // even when the intervening time components are absent (e.g. a year with an offset but
        // no month/day/time). Such a value is not a real instant and cannot be built into a
        // date, so it must be rejected rather than silently mis-parsed. This exercises the
        // second validation stage (createFromFormat failure), distinct from a pattern mismatch.
        $dtm = new DTM();

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dtm->setValue('2024+0500');
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
    public function testOutOfRangeComponentIsRejected(string $value): void
    {
        $dtm = new DTM();

        $this->expectException(InvalidDateTime::class);
        $this->expectExceptionMessage('HL7 expected date/time');

        $dtm->setValue($value);
    }
}
