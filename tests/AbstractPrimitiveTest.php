<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Tests\Fixtures\FakePrimitive;

#[CoversClass(AbstractPrimitive::class)]
final class AbstractPrimitiveTest extends TestCase
{
    public function testDerivesItsNameFromTheConcreteClass(): void
    {
        // The primitive's name is taken from the class and surfaces in diagnostics.
        $this->assertSame('FakePrimitive', new FakePrimitive()->getName());
    }

    public function testFieldNameDefaultsToUndefinedUntilAssigned(): void
    {
        // getName() reports the shared data-type (class) name; the field name is the distinct
        // schema field a value fills and is only assigned when materialized from a definition.
        // Until then it reports a sentinel, not '', so an unassigned field is distinguishable
        // from one legitimately named the empty string.
        $this->assertSame('<undefined>', new FakePrimitive()->getField());
    }

    public function testSetFieldRecordsTheFieldNameForLaterRetrieval(): void
    {
        // The definition stamps its field name onto every value it materializes; getField must
        // report exactly what was set so the value can say which schema field it belongs to.
        $primitive = new FakePrimitive();
        $primitive->setField('Patient Name');

        $this->assertSame('Patient Name', $primitive->getField());
    }

    public function testParseSplitsOnlyOnTheSubcomponentSeparator(): void
    {
        // A primitive sits at the bottom of the separator hierarchy: its parts are subcomponents,
        // split by "&". The leading part is the value; every trailing part is a subcomponent,
        // preserving depth rather than being discarded or merged with component-level data.
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), 'value&sub');

        $this->assertSame('value', $primitive->getValue());
        $this->assertCount(1, $primitive->getExtraComponents());
    }

    public function testParseKeepsALiteralComponentSeparatorInTheValue(): void
    {
        // A component separator ("^") that reaches a primitive is literal value data, not a
        // structural split: the primitive is already below the component level, so it must
        // never carve "^" into extra components.
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), 'left^right');

        $this->assertSame('left^right', $primitive->getValue());
        $this->assertCount(0, $primitive->getExtraComponents());
    }

    public function testReparsingDropsSubcomponentsFromAnEarlierParse(): void
    {
        // clear() runs on every parse() so a primitive can be re-parsed. If the extra subcomponents
        // survive that reset, a later parse without a "&" leaves the earlier subcomponent behind --
        // re-parse must reflect only the latest data, never leak prior structure.
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), 'a&b');
        $primitive->parse(new Encoding(), 'x');

        $this->assertSame('x', $primitive->getValue());
        $this->assertCount(0, $primitive->getExtraComponents());
    }

    public function testParseClearsTheValueForEmptyData(): void
    {
        // Re-parsing empty data must reset any previously held value.
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), 'seed');
        $primitive->parse(new Encoding(), '');

        $this->assertSame('', $primitive->getValue());
    }

    public function testSerializeRoundTripsAPlainValue(): void
    {
        // serialize is the inverse of parse: a decoded value is re-encoded to the same bytes.
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), 'value');

        $this->assertSame('value', $primitive->serialize(new Encoding()));
    }

    public function testSerializeReEncodesSeparatorCharactersInTheValue(): void
    {
        // A component separator that reached the primitive is literal data; serialize must escape
        // it, so parse(serialize(x)) recovers the same value.
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), 'a\\S\\b'); // "\S\" decodes to "^"

        $this->assertSame('a^b', $primitive->getValue());
        $this->assertSame('a\\S\\b', $primitive->serialize(new Encoding()));
    }

    public function testSerializeEmitsSubcomponentsJoinedByTheSubcomponentSeparator(): void
    {
        // Extra parts are subcomponents; they rejoin with "&" at the primitive's own level.
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), 'a&b&c');

        $this->assertSame('a&b&c', $primitive->serialize(new Encoding()));
    }

    public function testSerializeTrimsTrailingEmptySubcomponents(): void
    {
        // Trailing empty subcomponents are canonical noise and collapse away.
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), 'a&b&');

        $this->assertSame('a&b', $primitive->serialize(new Encoding()));
    }

    public function testSerializeKeepsInteriorEmptySubcomponents(): void
    {
        // An interior empty subcomponent is positional and must survive.
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), 'a&&c');

        $this->assertSame('a&&c', $primitive->serialize(new Encoding()));
    }

    public function testSerializeOfEmptyDataIsEmpty(): void
    {
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), '');

        $this->assertSame('', $primitive->serialize(new Encoding()));
    }
}
