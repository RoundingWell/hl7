<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Exception;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\HL7Exception;
use RoundingWell\HL7\Exception\InvalidMessage;

final class InvalidMessageTest extends TestCase
{
    public function testItIsAnHl7Exception(): void
    {
        // Callers catch HL7Exception to handle any parser failure uniformly.
        $this->assertInstanceOf(HL7Exception::class, InvalidMessage::missingMSH());
    }

    public function testItIsAnInvalidArgumentException(): void
    {
        // A malformed message is a caller-supplied argument problem, not a runtime fault.
        $this->assertInstanceOf(InvalidArgumentException::class, InvalidMessage::missingMSH());
    }

    public function testMissingMshExplainsTheRequiredFirstSegment(): void
    {
        $this->assertSame("HL7 message must start with 'MSH' segment", InvalidMessage::missingMSH()->getMessage());
    }

    public function testMissingDelimiterExplainsTheRequiredDelimiter(): void
    {
        $this->assertSame(
            "HL7 message must have a delimiter in the 'MSH' segment",
            InvalidMessage::missingDelimiter()->getMessage(),
        );
    }

    public function testInvalidEncodingExplainsTheRequiredEncodingCharacters(): void
    {
        $this->assertSame(
            "HL7 message must have 4 encoding characters in the 'MSH' segment",
            InvalidMessage::invalidEncoding()->getMessage(),
        );
    }
}
