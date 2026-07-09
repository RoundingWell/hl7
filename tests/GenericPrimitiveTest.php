<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\ExtraComponents;
use RoundingWell\HL7\GenericPrimitive;
use RoundingWell\HL7\Primitive;

#[CoversClass(GenericPrimitive::class)]
final class GenericPrimitiveTest extends TestCase
{
    public function testNameIsUnknown(): void
    {
        // A generic primitive stands in for an unrecognised type, so it must not
        // masquerade as any named HL7 data type.
        $primitive = new GenericPrimitive();

        $this->assertSame('UNKNOWN', $primitive->getName());
    }

    public function testUnsetValueIsEmpty(): void
    {
        // A freshly constructed primitive holds no data, not a leftover default.
        $primitive = new GenericPrimitive();

        $this->assertSame('', $primitive->getValue());
    }

    public function testSetValueStoresTheValueVerbatim(): void
    {
        // setValue is the plain-text setter: it must store exactly what it is given,
        // with no encoding or decoding applied.
        $primitive = new GenericPrimitive();
        $primitive->setValue('A|B');

        $this->assertSame('A|B', $primitive->getValue());
    }

    public function testClearResetsTheValue(): void
    {
        // Clearing must return the primitive to its unset state so a reused instance
        // cannot leak a prior value.
        $primitive = new GenericPrimitive();
        $primitive->setValue('populated');
        $primitive->clear();

        $this->assertSame('', $primitive->getValue());
    }

    public function testExtraComponentsAreStableAndInitiallyEmpty(): void
    {
        // The extra-components collection must be a single shared instance across
        // calls, otherwise components written through one reference would be
        // invisible through another. A primitive with no parsed data holds none.
        $primitive = new GenericPrimitive();

        $extra = $primitive->getExtraComponents();

        $this->assertInstanceOf(ExtraComponents::class, $extra);
        $this->assertSame($extra, $primitive->getExtraComponents());
        $this->assertCount(0, $extra);
    }

    public function testParseDecodesPlainDataIntoTheValue(): void
    {
        // Data with no component or subcomponent separators is a single primitive
        // value: the escaped raw form must be decoded and stored, with no extra
        // components produced.
        $primitive = new GenericPrimitive();
        $primitive->parse(new Encoding(), 'A\\F\\B');

        $this->assertSame('A|B', $primitive->getValue());
        $this->assertCount(0, $primitive->getExtraComponents());
    }

    public function testParseTreatsEmptyDataAsAbsent(): void
    {
        // Empty input carries no separators and no value, so it must leave the
        // primitive unset rather than fabricating an empty extra component.
        $primitive = new GenericPrimitive();
        $primitive->parse(new Encoding(), '');

        $this->assertSame('', $primitive->getValue());
        $this->assertCount(0, $primitive->getExtraComponents());
    }

    public function testParseSplitsSeparatedDataIntoValueAndExtraComponents(): void
    {
        // Data carrying component (^) or subcomponent (&) separators is flattened:
        // the first piece is the primitive's value, and every remaining piece is
        // captured as proprietary extra data rather than folded into the value.
        $primitive = new GenericPrimitive();
        $primitive->parse(new Encoding(), 'A^B&C');

        $this->assertSame('A', $primitive->getValue());
        $this->assertSame(['B', 'C'], self::extraValues($primitive->getExtraComponents()));
    }

    public function testParseDecodesTheValueAndEachExtraComponent(): void
    {
        // Splitting happens on the raw separators, but every stored piece -- both the
        // value and each extra component -- must be the decoded text so an escaped
        // delimiter is preserved as a literal instead of being split on.
        $primitive = new GenericPrimitive();
        $primitive->parse(new Encoding(), 'X\\F\\Y^P\\F\\Q');

        $this->assertSame('X|Y', $primitive->getValue());
        $this->assertSame(['P|Q'], self::extraValues($primitive->getExtraComponents()));
    }

    /**
     * @return list<string>
     */
    private static function extraValues(ExtraComponents $extra): array
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
