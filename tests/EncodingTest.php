<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;

#[CoversClass(Encoding::class)]
final class EncodingTest extends TestCase
{
    public function testShouldHaveDefaultValues(): void
    {
        $encoding = new Encoding();

        $this->assertSame("\r", $encoding->lineEnding);
        $this->assertSame('|', $encoding->fieldSeparator);
        $this->assertSame('^', $encoding->componentSeparator);
        $this->assertSame('~', $encoding->repetitionSeparator);
        $this->assertSame('\\', $encoding->escapeCharacter);
        $this->assertSame('&', $encoding->subcomponentSeparator);
        $this->assertSame('^~\\&', $encoding->encodingCharacters());
    }

    public function testShouldSupportCustomEscapeCharacters(): void
    {
        $encoding = new Encoding("\n", '!', '%', '$', '/', '#');

        $this->assertSame("\n", $encoding->lineEnding);
        $this->assertSame('!', $encoding->fieldSeparator);
        $this->assertSame('%', $encoding->componentSeparator);
        $this->assertSame('$', $encoding->repetitionSeparator);
        $this->assertSame('/', $encoding->escapeCharacter);
        $this->assertSame('#', $encoding->subcomponentSeparator);
        $this->assertSame('%$/#', $encoding->encodingCharacters());
    }

    public function testDecodeReplacesStandardEscapeSequences(): void
    {
        $encoding = new Encoding();

        // \F\ \S\ \T\ \R\ \E\ decode to the delimiter characters they name.
        $this->assertSame('a|b^c&d~e\\f', $encoding->decode('a\\F\\b\\S\\c\\T\\d\\R\\e\\E\\f'));
    }

    public function testDecodeLeavesPlainTextUntouched(): void
    {
        $encoding = new Encoding();

        $this->assertSame('SMITH', $encoding->decode('SMITH'));
    }

    public function testDecodePreservesUnknownEscapeSequences(): void
    {
        $encoding = new Encoding();

        // Formatting/hex escapes we do not interpret survive verbatim for round-trip.
        $this->assertSame('a\\.br\\b', $encoding->decode('a\\.br\\b'));
    }

    public function testDecodeKeepsDanglingEscapeCharacter(): void
    {
        $encoding = new Encoding();

        $this->assertSame('a\\b', $encoding->decode('a\\b'));
    }

    public function testEncodeEscapesDelimiterCharacters(): void
    {
        $encoding = new Encoding();

        // Round-trip: encode then decode returns the original.
        $original = 'a|b^c&d~e\\f';
        $encoded = $encoding->encode($original);

        $this->assertSame('a\\F\\b\\S\\c\\T\\d\\R\\e\\E\\f', $encoded);
        $this->assertSame($original, $encoding->decode($encoded));
    }
}
