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

    public function testParseRetainsTheLeadingValueAndCapturesTrailingSubcomponentsAsExtras(): void
    {
        // A primitive is a single value: the first component is the value, and any additional
        // components are preserved as extra components rather than being discarded.
        $primitive = new FakePrimitive();
        $primitive->parse(new Encoding(), 'value^surplus');

        $this->assertSame('value', $primitive->getValue());
        $this->assertCount(1, $primitive->getExtraComponents());
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
