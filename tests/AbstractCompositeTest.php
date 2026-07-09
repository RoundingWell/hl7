<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Primitive;
use RoundingWell\HL7\Tests\Fixtures\FakeComposite;

#[CoversClass(AbstractComposite::class)]
final class AbstractCompositeTest extends TestCase
{
    public function testDerivesItsNameFromTheConcreteClass(): void
    {
        // The composite's name drives error messages and is taken from the class, not the data.
        $this->assertSame('FakeComposite', new FakeComposite()->getName());
    }

    public function testGetComponentReturnsTheSameInstanceOnRepeatedAccess(): void
    {
        // Component access is idempotent: a second read must not discard parsed state.
        $composite = new FakeComposite();

        $first = $composite->getComponent(0);

        $this->assertSame($first, $composite->getComponent(0));
    }

    public function testGetComponentsReturnsEveryDefinedComponentInOrder(): void
    {
        // getComponents materializes the full, ordered component list callers iterate over.
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), 'alpha^beta');

        $components = $composite->getComponents();

        $this->assertCount(2, $components);
        $this->assertSame('alpha', $this->valueOf($components[0]));
        $this->assertSame('beta', $this->valueOf($components[1]));
    }

    public function testComponentsBeyondTheSchemaAreCapturedAsExtraComponents(): void
    {
        // A value with more components than the schema defines must not be dropped or throw;
        // the surplus is retained as extra components so nothing is silently lost.
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), 'a^b^x1^x2');

        $extra = $composite->getExtraComponents();

        $this->assertCount(2, $extra);
    }

    public function testParseLeavesComponentsUntouchedForEmptyData(): void
    {
        // Empty field data represents "no value", so nothing should be populated.
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), '');

        $this->assertCount(0, $composite->getExtraComponents());
    }

    public function testParseFallsBackToSubcomponentDelimiterWhenNoComponentsPresent(): void
    {
        // When a value carries only subcomponent delimiters, it is still split into components
        // so nested data is not treated as a single opaque string.
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), 'first&second');

        $this->assertSame('first', $this->valueOf($composite->getComponent(0)));
        $this->assertSame('second', $this->valueOf($composite->getComponent(1)));
    }

    public function testParseAssignsASingleUndelimitedValueToTheFirstComponent(): void
    {
        // A plain value with no delimiters maps to the first component.
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), 'solo');

        $this->assertSame('solo', $this->valueOf($composite->getComponent(0)));
    }

    private function valueOf(mixed $component): string
    {
        $this->assertInstanceOf(Primitive::class, $component);

        return $component->getValue();
    }
}
