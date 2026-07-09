<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\Generic;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidPath;

#[CoversClass(Generic::class)]
final class GenericTest extends TestCase
{
    public function testNewInstanceHoldsAnEmptyValue(): void
    {
        // An unpopulated generic field must read as empty rather than error.
        $generic = new Generic();

        $this->assertSame([], $generic->getValue());
    }

    public function testSetRawTreatsEmptyInputAsAbsent(): void
    {
        // An empty field is "absent"; it must not produce a stray empty-string element.
        $generic = new Generic();
        $generic->setRaw(new Encoding(), '');

        $this->assertSame([], $generic->getValue());
    }

    public function testSetRawStoresASingleValueAsAOneElementList(): void
    {
        // A value with no separators is still stored positionally so getPath('1') can reach it.
        $generic = new Generic();
        $generic->setRaw(new Encoding(), 'A');

        $this->assertSame(['A'], $generic->getValue());
    }

    public function testSetRawSplitsComponentsIntoANestedList(): void
    {
        // Component position carries meaning, so each component must become its own element.
        $generic = new Generic();
        $generic->setRaw(new Encoding(), 'A^B^C');

        $this->assertSame([['A', 'B', 'C']], $generic->getValue());
    }

    public function testSetRawSplitsSubcomponentsIntoADeeperList(): void
    {
        // Subcomponents nest under their component so the full structure is preserved.
        $generic = new Generic();
        $generic->setRaw(new Encoding(), 'A^B&C^D');

        $this->assertSame([['A', ['B', 'C'], 'D']], $generic->getValue());
    }

    public function testSetRawSplitsBeforeDecodingSoEscapedSeparatorsStayLiteral(): void
    {
        // Splitting must happen on raw separators; an escaped '^' is data, not a boundary.
        $generic = new Generic();
        $generic->setRaw(new Encoding(), 'A\\S\\B');

        $this->assertSame(['A^B'], $generic->getValue());
    }

    public function testSetRawDecodesEachValueComponentAndSubcomponent(): void
    {
        // Every leaf arrives escaped; the stored structure must hold decoded text throughout.
        $generic = new Generic();
        $generic->setRaw(new Encoding(), 'A\\F\\B^C&D\\F\\E');

        $this->assertSame([['A|B', ['C', 'D|E']]], $generic->getValue());
    }

    public function testSetValueAndGetValueRoundTrip(): void
    {
        // The value may be assigned directly; getValue must return it unchanged.
        $generic = new Generic();
        $generic->setValue(['A', 'B']);

        $this->assertSame(['A', 'B'], $generic->getValue());
    }

    public function testGetPathReturnsTheTopLevelValue(): void
    {
        // getPath is 1-based, so '1' addresses the first value element.
        $generic = new Generic();
        $generic->setRaw(new Encoding(), 'A^B^C');

        $this->assertSame(['A', 'B', 'C'], $generic->getPath('1'));
    }

    public function testGetPathReturnsANestedComponent(): void
    {
        // A dotted path walks into each level; '1.2' is the second component of the first value.
        $generic = new Generic();
        $generic->setRaw(new Encoding(), 'A^B^C');

        $this->assertSame('B', $generic->getPath('1.2'));
    }

    public function testGetPathReturnsANestedSubcomponent(): void
    {
        // The third level addresses subcomponents; '1.2.2' is the second subcomponent of component two.
        $generic = new Generic();
        $generic->setRaw(new Encoding(), 'A^B&C^D');

        $this->assertSame('C', $generic->getPath('1.2.2'));
    }

    public function testGetPathReturnsNullWhenAnIndexIsMissing(): void
    {
        // A path past the available data is absent, not an error.
        $generic = new Generic();
        $generic->setRaw(new Encoding(), 'A^B');

        $this->assertNull($generic->getPath('1.3'));
    }

    public function testGetPathReturnsNullWhenDescendingIntoAScalar(): void
    {
        // Once a path reaches a string, further segments cannot descend and must return null.
        $generic = new Generic();
        $generic->setRaw(new Encoding(), 'A^B');

        $this->assertNull($generic->getPath('1.1.1'));
    }

    public function testGetPathReturnsNullOnAnEmptyValue(): void
    {
        // With nothing stored, any path is absent.
        $generic = new Generic();

        $this->assertNull($generic->getPath('1'));
    }

    public function testGetPathRejectsANonNumericPath(): void
    {
        // A path is a positional index, so a non-numeric segment is a programming error and must fail loud.
        $generic = new Generic();

        $this->expectException(InvalidPath::class);
        $this->expectExceptionMessageIsOrContains('Dot path must only contain dots and numeric values, got: name');

        $generic->getPath('name');
    }

    public function testGetPathRejectsAnEmptyPath(): void
    {
        // An empty path addresses nothing; it is rejected rather than silently treated as absent.
        $generic = new Generic();

        $this->expectException(InvalidPath::class);

        $generic->getPath('');
    }

    public function testGetPathRejectsAMalformedNumericPath(): void
    {
        // Every dot must separate two indexes; a trailing or empty segment is malformed, not index zero.
        $generic = new Generic();

        $this->expectException(InvalidPath::class);

        $generic->getPath('1.');
    }
}
