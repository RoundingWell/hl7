<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Exception;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\HL7Exception;
use RoundingWell\HL7\Exception\InvalidPath;

#[CoversClass(InvalidPath::class)]
final class InvalidPathTest extends TestCase
{
    public function testItIsAnHl7Exception(): void
    {
        // Callers catch HL7Exception to handle any parser failure uniformly.
        $this->assertInstanceOf(HL7Exception::class, InvalidPath::notNumeric('name'));
    }

    public function testItIsAnInvalidArgumentException(): void
    {
        // A non-numeric dot path is caller-supplied bad input, not a runtime condition.
        $this->assertInstanceOf(InvalidArgumentException::class, InvalidPath::notNumeric('name'));
    }

    public function testNotNumericIncludesTheOffendingPath(): void
    {
        // The rejected path must appear in the message so the caller can spot the bad argument.
        $this->assertSame(
            'Dot path must only contain dots and numeric values, got: name',
            InvalidPath::notNumeric('name')->getMessage(),
        );
    }
}
