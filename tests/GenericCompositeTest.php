<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\ExtraComponents;
use RoundingWell\HL7\GenericComposite;
use RoundingWell\HL7\Primitive;

#[CoversClass(GenericComposite::class)]
final class GenericCompositeTest extends TestCase
{
    public function testNameIsUnknown(): void
    {
        // A generic composite stands in for an unrecognised composite, so it must not
        // masquerade as any named HL7 data type.
        $this->assertSame('UNKNOWN', new GenericComposite()->getName());
    }

    public function testEachComponentSeparatorProducesItsOwnComponent(): void
    {
        // With no schema, every "^"-delimited part overflows to an extra component. This is the
        // two-level model: a composite of primitives, one component per "^", nothing flattened.
        $composite = new GenericComposite();
        $composite->parse(new Encoding(), 'a^b^c');

        $this->assertSame(['a', 'b', 'c'], self::componentValues($composite->getExtraComponents()));
    }

    public function testAComponentWithSubcomponentsStaysASingleComponent(): void
    {
        // "a&b" with no "^" is ONE component "a" carrying subcomponent "b" -- not two components.
        // The generic composite must never infer a component boundary from a "&".
        $composite = new GenericComposite();
        $composite->parse(new Encoding(), 'a&b');

        $extra = $composite->getExtraComponents();
        $this->assertCount(1, $extra);
        $this->assertSame('a', self::componentValues($extra)[0]);

        $subcomponents = $extra->getComponent(0)->getExtraComponents();
        $this->assertCount(1, $subcomponents);
        $this->assertSame('b', self::componentValues($subcomponents)[0]);
    }

    /**
     * @return list<string>
     */
    private static function componentValues(ExtraComponents $extra): array
    {
        $values = [];

        for ($index = 0; $index < count($extra); $index++) {
            $data = $extra->getComponent($index)->getData();
            self::assertInstanceOf(Primitive::class, $data);
            $values[] = $data->getValue();
        }

        return $values;
    }
}
