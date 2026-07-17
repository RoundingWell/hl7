<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Message;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Message\ACK;

#[CoversClass(ACK::class)]
final class ACKTest extends TestCase
{
    public function testParsesMshAndMsaSegments(): void
    {
        // An ACK is defined as MSH + MSA; parsing a wire ACK must expose both segments
        // through their typed accessors so a caller can read the acknowledgment result.
        $ack = new ACK();
        $ack->parse(
            new Encoding("\r"),
            implode("\r", [
                'MSH|^~\\&|App|Fac|||20260101120000||ACK^A01^ACK|CTRL1|P|2.5.1',
                'MSA|AA|REQCTRL9',
            ]),
        );

        $this->assertSame('CTRL1', $ack->getMSH()->getMessageControlId()->getValue());
        $this->assertSame('AA', $ack->getMSA()->getAcknowledgmentCode()->getValue());
        $this->assertSame('REQCTRL9', $ack->getMSA()->getMessageControlId()->getValue());
    }
}
