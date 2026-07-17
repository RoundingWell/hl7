<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\MSA;

#[CoversClass(MSA::class)]
final class MSATest extends TestCase
{
    private MSA $msa;

    #[Override]
    protected function setUp(): void
    {
        $this->msa = new MSA();
        $this->msa->parse(new Encoding(), implode('|', [
            'MSA', // Segment name
            'AA', // MSA.1 Acknowledgment Code
            'MSGCTRL1', // MSA.2 Message Control ID
            'Everything is fine', // MSA.3 Text Message
            '42', // MSA.4 Expected Sequence Number
        ]));
    }

    public function testFieldsMapToTheirValues(): void
    {
        // MSA-1/MSA-2 are what a receiver correlates against; all modeled fields must
        // map to the value parsed at their position, not shift or collapse.
        $this->assertSame('AA', $this->msa->getAcknowledgmentCode()->getValue());
        $this->assertSame('MSGCTRL1', $this->msa->getMessageControlId()->getValue());
        $this->assertSame('Everything is fine', $this->msa->getTextMessage()->getValue());
        $this->assertSame('42', $this->msa->getExpectedSequenceNumber()->getValue());
    }
}
