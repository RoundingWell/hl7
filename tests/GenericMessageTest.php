<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\GenericMessage;
use RoundingWell\HL7\Segment\MSH;

#[CoversClass(GenericMessage::class)]
final class GenericMessageTest extends TestCase
{
    public function testExposesTheNameAndVersionItWasConstructedWith(): void
    {
        // The message name identifies the structure in error messages and routing, so it must round-trip.
        // The HL7 version selects which schema applies to a message, so it must round-trip verbatim.
        $this->assertSame('ADT_A01', new GenericMessage('ADT_A01', '2.5.1')->getName());
        $this->assertSame('2.5.1', new GenericMessage('ADT_A01', '2.5.1')->getVersion());
    }

    public function testParseToleratesUnregisteredSegments(): void
    {
        // The fallback message must accept arbitrary segments without error, as BaseMessage did.
        $message = new GenericMessage('ADT_A99', '2.8.1');
        $message->parse(new Encoding("\r"), "MSH|^~\\&\rZZZ|anything");

        $this->assertCount(1, $message->getAll('ZZZ'));
    }

    public function testGetMshReturnsATypedMshSegment(): void
    {
        // Callers of the factory rely on getMSH() regardless of the concrete message type.
        $message = new GenericMessage('ADT_A99', '2.8.1');
        $message->parse(new Encoding("\r"), "MSH|^~\\&#|App");

        $this->assertInstanceOf(MSH::class, $message->getMSH());
        $this->assertSame('^~\\&#', $message->getMSH()->getEncodingCharacters()->getValue());
    }

    public function testParseIgnoresTrailingLineEnding(): void
    {
        // Messages commonly end with a trailing line ending; grouping by name must not turn it
        // into a phantom segment for the fallback message either.
        $message = new GenericMessage('ADT_A99', '2.8.1');
        $message->parse(new Encoding("\r"), "MSH|^~\\&\rZZZ|anything\r");

        $this->assertCount(1, $message->getAll('ZZZ'));
    }

    public function testParseRetainsNonContiguousRepetitionsOfASegment(): void
    {
        // A fallback for unknown messages must be lossless: OBX repeating around an NTE is a common
        // real HL7 shape, and the second OBX cluster must not silently vanish.
        $message = new GenericMessage('ORU_R01', '2.8.1');
        $message->parse(new Encoding("\r"), "MSH|^~\\&\rOBX|1\rNTE|1\rOBX|2");

        $this->assertCount(2, $message->getAll('OBX'));
        $this->assertCount(1, $message->getAll('NTE'));
    }
}
