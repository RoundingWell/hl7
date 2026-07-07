<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\MSG;
use RoundingWell\HL7\Encoding;

#[CoversClass(MSG::class)]
final class MSGTest extends TestCase
{
    public function testComponentsMapToMessageTypeTriggerAndStructure(): void
    {
        // The trigger event drives message routing, so component order must be exact.
        $msg = new MSG();
        $msg->setRaw(new Encoding(), 'ADT^A01^ADT_A01');

        $this->assertSame('ADT', $msg->messageType->getValue());
        $this->assertSame('A01', $msg->triggerEvent->getValue());
        $this->assertSame('ADT_A01', $msg->messageStructure->getValue());
    }
}
