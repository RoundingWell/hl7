<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\LineEnding;

#[CoversClass(LineEnding::class)]
final class LineEndingTest extends TestCase
{
    public function testCasesCarryTheirLiteralCharacters(): void
    {
        // Segments are joined/split on these exact bytes, so the values must be the raw characters.
        $this->assertSame("\r", LineEnding::CR->value);
        $this->assertSame("\n", LineEnding::LF->value);
        $this->assertSame("\r\n", LineEnding::CRLF->value);
    }

    public function testDefaultsToCarriageReturn(): void
    {
        // No line ending at all still resolves to a usable default rather than failing.
        $this->assertSame(LineEnding::CR, LineEnding::detect('MSH|^~\\&'));
    }

    public function testDetectsCarriageReturn(): void
    {
        $this->assertSame(LineEnding::CR, LineEnding::detect("MSH|^~\\&\r"));
    }

    public function testDetectsLineFeed(): void
    {
        $this->assertSame(LineEnding::LF, LineEnding::detect("MSH|^~\\&\n"));
    }

    public function testDetectsCombined(): void
    {
        $this->assertSame(LineEnding::CRLF, LineEnding::detect("MSH|^~\\&\r\n"));
    }

    public function testCombinedIsPreferredOverCarriageReturn(): void
    {
        // "\r\n" contains "\r", so CRLF must be checked first or a CRLF message
        // would be misclassified as CR and split incorrectly.
        $this->assertSame(LineEnding::CRLF, LineEnding::detect("A\r\nB"));
    }
}
