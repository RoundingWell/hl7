<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Exception;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\HL7Exception;
use RoundingWell\HL7\Exception\InvalidValue;

#[CoversClass(InvalidValue::class)]
final class InvalidValueTest extends TestCase
{
    public function testItIsAnHl7Exception(): void
    {
        // Callers catch HL7Exception to handle any parser failure uniformly.
        $this->assertInstanceOf(HL7Exception::class, InvalidValue::minLength('ST', 1));
    }

    public function testItIsAnInvalidArgumentException(): void
    {
        // A value outside the type's constraints is a bad caller-supplied argument.
        $this->assertInstanceOf(InvalidArgumentException::class, InvalidValue::minLength('ST', 1));
    }

    public function testMinLengthStatesTheRequiredMinimum(): void
    {
        $this->assertSame(
            'Value of ST must be at least 4 characters long',
            InvalidValue::minLength('ST', 4)->getMessage(),
        );
    }

    public function testMaxLengthStatesTheAllowedMaximum(): void
    {
        $this->assertSame(
            'Value of ST must be at most 4 characters long',
            InvalidValue::maxLength('ST', 4)->getMessage(),
        );
    }

    public function testMinValueStatesTheLowerBound(): void
    {
        $this->assertSame(
            'Value of NM must be greater than or equal to 0',
            InvalidValue::minValue('NM', 0)->getMessage(),
        );
    }

    public function testMaxValueStatesTheUpperBound(): void
    {
        $this->assertSame(
            'Value of NM must be less than or equal to 100',
            InvalidValue::maxValue('NM', 100)->getMessage(),
        );
    }

    public function testNotNumericStatesTheOffendingValue(): void
    {
        $this->assertSame(
            'Value of SNM must be numeric, got 12A',
            InvalidValue::notNumeric('SNM', '12A')->getMessage(),
        );
    }
}
