<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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

    /**
     * encode() must invert decode(): for any validly-encoded wire string, decoding
     * it and re-encoding must reproduce the original bytes. This is what lets us
     * decode a message, mutate unrelated fields, and re-encode without silently
     * corrupting preserved formatting/hex escapes the reader still needs.
     *
     * @param non-empty-string $encoded
     */
    #[DataProvider('validlyEncodedStrings')]
    public function testEncodeIsInverseOfDecode(string $encoded): void
    {
        $encoding = new Encoding();

        $this->assertSame($encoded, $encoding->encode($encoding->decode($encoded)));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function validlyEncodedStrings(): iterable
    {
        yield 'plain text' => ['SMITH'];
        yield 'all structural escapes' => ['a\\F\\b\\S\\c\\T\\d\\R\\e\\E\\f'];
        yield 'preserved line break' => ['a\\.br\\b'];
        yield 'preserved hex escape' => ['\\X0A\\'];
        yield 'preserved space command' => ['\\.sp\\'];
        yield 'preserved next to structural' => ['a\\.br\\b\\F\\c\\E\\d'];
        yield 'empty preserved escape' => ['\\\\'];
    }

    /**
     * decode() must invert encode(): any value a caller sets must survive an
     * encode/decode round-trip unchanged. The fixed encoded forms are asserted too
     * because the spec pins them, but the property under test is the round-trip.
     *
     * @param non-empty-string $value
     * @param non-empty-string $expectedEncoded
     */
    #[DataProvider('roundTripValues')]
    public function testDecodeIsInverseOfEncode(string $value, string $expectedEncoded): void
    {
        $encoding = new Encoding();

        $encoded = $encoding->encode($value);

        $this->assertSame($expectedEncoded, $encoded);
        $this->assertSame($value, $encoding->decode($encoded));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function roundTripValues(): iterable
    {
        // A value that looks like a formatting command is emitted verbatim (accepted
        // trade-off: decode() already treats \...\ as opaque) but still round-trips.
        yield 'formatting-command-like value' => ['\\.br\\', '\\.br\\'];
        yield 'literal escape sequence' => ['\\E\\', '\\E\\E\\E\\'];
        yield 'lone escape character' => ['\\', '\\E\\'];
        yield 'field separator' => ['|', '\\F\\'];
        yield 'lone escape mid-string' => ['a\\b', 'a\\E\\b'];
        yield 'plain text' => ['SMITH', 'SMITH'];
    }
}
