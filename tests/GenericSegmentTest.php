<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\ExtraComponents;
use RoundingWell\HL7\GenericComposite;
use RoundingWell\HL7\GenericSegment;
use RoundingWell\HL7\Primitive;

#[CoversClass(GenericSegment::class)]
final class GenericSegmentTest extends TestCase
{
    public function testExposesTheNameItWasConstructedWith(): void
    {
        // The segment identifier drives error messages and message routing, so it must round-trip verbatim.
        $this->assertSame('ZZZ', new GenericSegment('ZZZ')->getName());
    }

    public function testUndefinedFieldsPreserveComponentStructure(): void
    {
        // A field on an unknown segment has no schema, but its component structure must survive:
        // "a^b^c" is three components, not a single flattened value. This is why undefined fields
        // default to a composite -- so the "^" depth is respected instead of collapsed.
        $segment = new GenericSegment('ZXY');
        $segment->parse(new Encoding(), 'ZXY|a^b^c');

        $field = $segment->getFieldRepetition(1, 0);
        $this->assertInstanceOf(GenericComposite::class, $field);
        $this->assertSame(['a', 'b', 'c'], self::componentValues($field->getExtraComponents()));
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
