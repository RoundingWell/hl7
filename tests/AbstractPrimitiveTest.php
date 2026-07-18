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

    public function testParseClearsTheValueForEmptyData(): void
    {
        // Re-parsing empty data must reset any previously held value.
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), 'seed');
        $primitive->parse(new Encoding(), '');

        $this->assertSame('', $primitive->getValue());
    }
}
