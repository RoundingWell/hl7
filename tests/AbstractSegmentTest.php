<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\GenericComposite;
use RoundingWell\HL7\GenericPrimitive;
use RoundingWell\HL7\Tests\Fixtures\FakeSegment;
use RoundingWell\HL7\TypeDefinition;

#[CoversClass(AbstractSegment::class)]
final class AbstractSegmentTest extends TestCase
{
    public function testDerivesItsNameFromTheConcreteClass(): void
    {
        // A schema'd segment is identified by its class, which drives error messages and routing.
        $this->assertSame('FakeSegment', new FakeSegment()->getName());
    }

    public function testGetFieldReturnsAnEmptyListWhenNothingHasBeenSet(): void
    {
        // Callers iterate the returned list; an unpopulated field must be an empty list, not an error.
        $this->assertSame([], new FakeSegment()->getField(1));
    }

    public function testGetFieldRejectsNumbersBelowOne(): void
    {
        $segment = new FakeSegment();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/1 or greater/');

        $segment->getField(0);
    }

    public function testGetFieldRepetitionSynthesizesUndefinedFieldsAsGenericComposite(): void
    {
        // A field sits above the component level, so an undefined one defaults to a
        // GenericComposite: this preserves any component structure the field carries instead of
        // flattening it into a primitive.
        $segment = new FakeSegment();

        $this->assertInstanceOf(GenericComposite::class, $segment->getFieldRepetition(3, 0));
    }

    public function testGetFieldRepetitionInstantiatesTheDefinedType(): void
    {
        // When a field is explicitly typed via its definition, that exact type must back the instance.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition(type: GenericPrimitive::class));

        $this->assertInstanceOf(GenericPrimitive::class, $segment->getFieldRepetition(1, 0));
    }

    public function testGetFieldRepetitionReturnsTheSameInstanceOnRepeatedAccess(): void
    {
        // Field access is idempotent: repeated reads of the same repetition must not discard parsed state.
        $segment = new FakeSegment();

        $first = $segment->getFieldRepetition(1, 0);

        $this->assertSame($first, $segment->getFieldRepetition(1, 0));
    }

    public function testGetFieldRepetitionRejectsNegativeRepetitions(): void
    {
        // Repetitions are 0-based indexes; a negative repetition is meaningless.
        $segment = new FakeSegment();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/0 or greater/');

        $segment->getFieldRepetition(1, -1);
    }

    public function testFieldsAllowMultipleRepetitionsByDefault(): void
    {
        // maxReps defaults to 0 ("no limit"), so a field accepts additional repetitions without being declared.
        $segment = new FakeSegment();
        $segment->getFieldRepetition(1, 0);
        $segment->getFieldRepetition(1, 1);

        $this->assertSame(2, $segment->getLength(1));
    }

    public function testFieldRejectsRepetitionsBeyondItsMaxReps(): void
    {
        // A capped field must refuse repetitions past its declared maximum so malformed input can't grow it unbounded.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition('Cap', maxReps: 2));
        $segment->getFieldRepetition(1, 0);
        $segment->getFieldRepetition(1, 1);
        $segment->getFieldRepetition(1, 2);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessageMatches('/only 2 repetitions are allowed/');

        $segment->getFieldRepetition(1, 3);
    }

    public function testRepetitionsCannotSkipPastTheOnesAlreadyCreated(): void
    {
        // Repetitions are contiguous; requesting an index beyond the next one would leave a gap, so it is rejected.
        $segment = new FakeSegment();

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessageMatches('/there are only 0 repetitions/');

        $segment->getFieldRepetition(1, 2);
    }

    public function testParsePopulatesFieldsPositionallyIgnoringTheSegmentName(): void
    {
        // HL7 field 1 is the first value after the segment name, so parsing must skip index 0 (the name itself).
        $segment = new FakeSegment();
        $segment->parse(new Encoding(), 'ZZZ|alpha|beta');

        $this->assertSame('alpha', $this->fieldValue($segment, 1, 0));
        $this->assertSame('beta', $this->fieldValue($segment, 2, 0));
    }

    public function testParseSplitsRepeatingFieldsIntoSeparateRepetitions(): void
    {
        // A field carrying multiple values separated by "~" must expand into one repetition per value,
        // while neighbours without a "~" remain a single repetition.
        $segment = new FakeSegment();

        $segment->parse(new Encoding(), 'ZZZ|a|b~c~d|e');

        $this->assertSame(1, $segment->getLength(1));
        $this->assertSame(3, $segment->getLength(2));
        $this->assertSame(1, $segment->getLength(3));

        $this->assertSame('a', $this->fieldValue($segment, 1, 0));
        $this->assertSame('b', $this->fieldValue($segment, 2, 0));
        $this->assertSame('c', $this->fieldValue($segment, 2, 1));
        $this->assertSame('d', $this->fieldValue($segment, 2, 2));
        $this->assertSame('e', $this->fieldValue($segment, 3, 0));
    }

    private function fieldValue(FakeSegment $segment, int $number, int $repetition): string
    {
        $field = $segment->getFieldRepetition($number, $repetition);
        $this->assertInstanceOf(GenericComposite::class, $field);

        // An undefined field is a schema-less composite, so a plain value lands as its single
        // overflow component: a Varies wrapping a GenericPrimitive.
        $data = $field->getExtraComponents()->getComponent(0)->getData();
        $this->assertInstanceOf(GenericPrimitive::class, $data);

        return $data->getValue();
    }

    public function testGetFieldCountReflectsTheNumberOfDefinedFields(): void
    {
        // getFieldCount drives how many fields the parser and serializers iterate over.
        $segment = new FakeSegment();
        $segment->parse(new Encoding(), 'ZZZ|a|b|c');

        $this->assertSame(3, $segment->getFieldCount());
    }

    public function testExposesTheNamesOfAddedFieldsInOrder(): void
    {
        // getNames drives human-readable field labels, so each added field must surface its name positionally.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition('Set ID'));
        $segment->add(new TypeDefinition('Patient Name'));

        $this->assertSame(['Set ID', 'Patient Name'], $segment->getNames());
    }

    public function testReportsTheNumberOfRepetitionsForAField(): void
    {
        // getLength drives how many repetitions a caller iterates for a repeating field.
        $segment = new FakeSegment();
        $segment->getFieldRepetition(1, 0);

        $this->assertSame(1, $segment->getLength(1));
    }

    public function testReportsWhetherAFieldIsRequired(): void
    {
        // The required flag is metadata used for validation and must survive registration unchanged.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition(isRequired: true));

        $this->assertTrue($segment->isRequired(1));
    }

    public function testIsRequiredRejectsFieldsThatWereNeverAdded(): void
    {
        // "required" is meaningless for a field the segment never declared; guessing false would hide schema gaps.
        $segment = new FakeSegment();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/FakeSegment\.1, it has not been added/');

        $segment->isRequired(1);
    }

    public function testReportsAFieldsMaxRepetitions(): void
    {
        // The repetition limit governs how many repetitions are permitted and must survive registration unchanged.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition(maxReps: 3));

        $this->assertSame(3, $segment->maxRepetitions(1));
    }

    public function testMaxRepetitionsRejectsFieldsThatWereNeverAdded(): void
    {
        // A repetition limit is meaningless for a field the segment never declared, so it must fail loudly.
        $segment = new FakeSegment();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/FakeSegment\.1, it has not been added/');

        $segment->maxRepetitions(1);
    }
}
