<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\EVN;

#[CoversClass(EVN::class)]
final class EVNTest extends TestCase
{
    private EVN $evn;

    #[Override]
    protected function setUp(): void
    {
        $this->evn = new EVN();
        $this->evn->parse(new Encoding(), implode('|', [
            'EVN', // Segment name
            'A01', // EVN.1 Event Type Code
            '20050110045502', // EVN.2 Recorded Date/Time
            '20050111', // EVN.3 Date/Time Planned Event
            'REASON^Admission', // EVN.4 Event Reason Code
            '37^DISNEY~38^MOUSE', // EVN.5 Operator ID (repeating)
            '20050112', // EVN.6 Event Occurred
            'Stallone General^^ISO', // EVN.7 Event Facility
        ]));
    }

    public function testScalarAndDateTimeFieldsMapToTheirValues(): void
    {
        $this->assertSame('A01', $this->evn->getTypeCode()->getValue());
        $this->assertSame('20050110045502', $this->evn->getRecordedDateTime()->getValue());
        $this->assertSame('20050111', $this->evn->getPlannedDateTime()->getValue());
        $this->assertSame('20050112', $this->evn->getOccurredDateTime()->getValue());
    }

    public function testCompositeFieldsMapToTheirLeadingComponents(): void
    {
        $this->assertSame('REASON', $this->evn->getEventReasonCode()->getIdentifier()->getValue());
        $this->assertSame('Stallone General', $this->evn->getEventFacility()->getNamespaceId()->getValue());
    }

    public function testOperatorIdCollectsEveryRepetition(): void
    {
        // EVN.5 is repeating, so each operator reference must be retained in order.
        $operators = $this->evn->getOperatorId();

        $this->assertCount(2, $operators);
        $this->assertSame('37', $operators[0]->getId()->getValue());
        $this->assertSame('DISNEY', $operators[0]->getFamilyName()->getSurname()->getValue());
        $this->assertSame('38', $operators[1]->getId()->getValue());
    }
}
