<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\Composite;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\ExtraComponents;
use RoundingWell\HL7\Primitive;
use RoundingWell\HL7\Tests\Fixtures\FakeComposite;
use RoundingWell\HL7\Tests\Fixtures\FakeNestedComposite;

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

    public function testAlwaysSplitsOnTheComponentSeparatorSoSubcomponentsStayInTheirComponent(): void
    {
        // Depth is fixed by the separator, not inferred from content: a composite ALWAYS splits
        // on "^". A "&" inside a component is that component's own subcomponent, so "a^b&c" is two
        // components -- "a" and "b" -- where "c" is a subcomponent of "b", never a third component.
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), 'a^b&c');

        $this->assertSame('a', $this->valueOf($composite->getComponent(0)));
        $this->assertSame('b', $this->valueOf($composite->getComponent(1)));

        $subcomponents = $composite->getComponent(1)->getExtraComponents();
        $this->assertCount(1, $subcomponents);
        $this->assertSame('c', $this->subcomponentValueOf($subcomponents, 0));
    }

    public function testDoesNotSplitAComponentOnTheSubcomponentSeparator(): void
    {
        // "a&b" with no "^" is a single component "a" carrying subcomponent "b", NOT two
        // components: the composite must not infer a component split from a "&".
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), 'first&second');

        $this->assertSame('first', $this->valueOf($composite->getComponent(0)));

        $subcomponents = $composite->getComponent(0)->getExtraComponents();
        $this->assertCount(1, $subcomponents);
        $this->assertSame('second', $this->subcomponentValueOf($subcomponents, 0));

        // The "&" did not spill into a second component.
        $this->assertSame('', $this->valueOf($composite->getComponent(1)));
    }

    public function testParseAssignsASingleUndelimitedValueToTheFirstComponent(): void
    {
        // A plain value with no delimiters maps to the first component.
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), 'solo');

        $this->assertSame('solo', $this->valueOf($composite->getComponent(0)));
    }

    public function testANestedCompositeComponentSplitsOnTheSubcomponentSeparator(): void
    {
        // Depth, not content, picks the separator: a composite nested as another composite's
        // component sits one level down, so ITS parts arrive as subcomponents ("&"), while the
        // outer composite still delimits on "^". "x&y^z" => inner composite (x, y), then "z".
        $composite = new FakeNestedComposite();
        $composite->parse(new Encoding(), 'x&y^z');

        $inner = $composite->getComponent(0);
        $this->assertInstanceOf(Composite::class, $inner);
        $this->assertSame('x', $this->valueOf($inner->getComponent(0)));
        $this->assertSame('y', $this->valueOf($inner->getComponent(1)));

        $this->assertSame('z', $this->valueOf($composite->getComponent(1)));
    }

    public function testSerializeRoundTripsComponents(): void
    {
        // Field-level composite: components rejoin with the component separator "^".
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), 'alpha^beta');

        $this->assertSame('alpha^beta', $composite->serialize(new Encoding()));
    }

    public function testSerializeRoundTripsAComponentThatCarriesASubcomponent(): void
    {
        // "a^b&c": the "&" belongs to component "b" and must serialize back inside it, not as a
        // third component -- proving depth is preserved through the round trip.
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), 'a^b&c');

        $this->assertSame('a^b&c', $composite->serialize(new Encoding()));
    }

    public function testSerializeRoundTripsExtraComponentsBeyondTheSchema(): void
    {
        // Surplus components are retained and re-emitted in order after the defined ones.
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), 'a^b^x1^x2');

        $this->assertSame('a^b^x1^x2', $composite->serialize(new Encoding()));
    }

    public function testSerializeTrimsTrailingEmptyComponents(): void
    {
        // A schema of two components holding only the first emits just that value, never "a^".
        $composite = new FakeComposite();
        $composite->parse(new Encoding(), 'a');

        $this->assertSame('a', $composite->serialize(new Encoding()));
    }

    public function testSerializeUsesTheSubcomponentSeparatorForANestedComposite(): void
    {
        // Depth, not content, picks the separator on the way out too: the inner composite sits one
        // level down, so its parts rejoin with "&" while the outer composite uses "^".
        $composite = new FakeNestedComposite();
        $composite->parse(new Encoding(), 'x&y^z');

        $this->assertSame('x&y^z', $composite->serialize(new Encoding()));
    }

    private function valueOf(mixed $component): string
    {
        $this->assertInstanceOf(Primitive::class, $component);

        return $component->getValue();
    }

    private function subcomponentValueOf(ExtraComponents $extra, int $index): string
    {
        return $this->valueOf($extra->getComponent($index)->getData());
    }
}
