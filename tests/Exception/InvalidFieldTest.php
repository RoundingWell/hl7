<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Exception;

use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\HL7Exception;
use RoundingWell\HL7\Exception\InvalidField;

#[CoversClass(InvalidField::class)]
final class InvalidFieldTest extends TestCase
{
    public function testItIsAnHl7Exception(): void
    {
        // Callers catch HL7Exception to handle any parser failure uniformly.
        $this->assertInstanceOf(HL7Exception::class, InvalidField::notDefined('PID', 5));
    }

    public function testItIsAnOutOfBoundsException(): void
    {
        // Addressing a field number the segment does not declare is an out-of-range access.
        $this->assertInstanceOf(OutOfBoundsException::class, InvalidField::notDefined('PID', 5));
    }

    public function testNotDefinedNamesTheSegmentAndFieldNumber(): void
    {
        // The segment id and field number must appear so the caller can locate the bad field.
        $this->assertSame("Field 'PID.5' is not defined", InvalidField::notDefined('PID', 5)->getMessage());
    }

    public function testTooLowNamesTheSegmentAndRejectedNumber(): void
    {
        // Field numbers are 1-based, so the message must call out the illegal number and the minimum.
        $this->assertSame(
            "Field 'MSH.0' is too low; minimum number is 1",
            InvalidField::tooLow('MSH', 0)->getMessage(),
        );
    }
}
