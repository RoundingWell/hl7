<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Exception;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\HL7Exception;
use RoundingWell\HL7\Exception\InvalidDate;

#[CoversClass(InvalidDate::class)]
final class InvalidDateTest extends TestCase
{
    public function testItIsAnHl7Exception(): void
    {
        // Callers catch HL7Exception to handle any parser failure uniformly.
        $this->assertInstanceOf(HL7Exception::class, InvalidDate::invalidValue('nope'));
    }

    public function testItIsAnInvalidArgumentException(): void
    {
        // A malformed date is a bad caller-supplied value, not a runtime fault.
        $this->assertInstanceOf(InvalidArgumentException::class, InvalidDate::invalidValue('nope'));
    }

    public function testInvalidValueEchoesTheRejectedValue(): void
    {
        // The offending value must appear so the caller can see what failed to parse.
        $this->assertSame(
            'HL7 expected date in format YYYYMMDD, got: nope',
            InvalidDate::invalidValue('nope')->getMessage(),
        );
    }
}
