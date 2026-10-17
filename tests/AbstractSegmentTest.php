<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DirtyFlag;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\ExtraComponents;
use RoundingWell\HL7\GenericComposite;
use RoundingWell\HL7\GenericPrimitive;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\SegmentElement;
use RoundingWell\HL7\Tests\Fixtures\FakeHydratingSegment;
use RoundingWell\HL7\Tests\Fixtures\FakeSegment;
use RoundingWell\HL7\TypeDefinition;
use RoundingWell\HL7\Varies;

#[CoversClass(AbstractSegment::class)]
#[CoversClass(AbstractComposite::class)]
#[CoversClass(AbstractPrimitive::class)]
#[CoversClass(DirtyFlag::class)]
#[CoversClass(ExtraComponents::class)]
#[CoversClass(TypeDefinition::class)]
#[CoversClass(Varies::class)]
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

    public function testSerializeRoundTripsAFieldWithComponents(): void
    {
        // A no-schema segment round-trips component structure because generic fields are composites.
        $segment = new FakeSegment();
        $segment->parse(new Encoding(), 'FakeSegment|a^b^c|d');

        $this->assertSame('FakeSegment|a^b^c|d', $segment->serialize(new Encoding()));
    }

    public function testSerializeRoundTripsRepeatingFields(): void
    {
        // Repetitions rejoin with the repetition separator "~".
        $segment = new FakeSegment();
        $segment->parse(new Encoding(), 'FakeSegment|a~b~c');

        $this->assertSame('FakeSegment|a~b~c', $segment->serialize(new Encoding()));
    }

    public function testSerializeTrimsTrailingEmptyFields(): void
    {
        // Trailing empty fields collapse; an interior empty field is preserved by position.
        $segment = new FakeSegment();
        $segment->parse(new Encoding(), 'FakeSegment|a||c||');

        $this->assertSame('FakeSegment|a||c', $segment->serialize(new Encoding()));
    }

    public function testSerializeOfAFieldlessSegmentIsJustItsName(): void
    {
        // A segment with no fields is canonical as its bare name; the field separator is only
        // written when a field follows it, so parse(name) round-trips exactly rather than
        // emitting a dangling "Name|".
        $segment = new FakeSegment();
        $segment->parse(new Encoding(), 'FakeSegment');

        $this->assertSame('FakeSegment', $segment->serialize(new Encoding()));
    }

    public function testFirstNamesIsTheSegmentsOwnName(): void
    {
        // A segment's FIRST-set is itself: groups match stream segments to structures with it,
        // without needing to know whether the structure is a segment or a group.
        $this->assertSame(['FakeSegment'], new FakeSegment()->firstNames());
    }

    public function testParseSegmentsConsumesExactlyOneElement(): void
    {
        // A segment claims a single line from the stream and returns the next offset, so an
        // enclosing group resumes immediately after it instead of losing its position.
        $segment = new FakeSegment();

        $next = $segment->parseSegments(
            new Encoding(),
            [
                new SegmentElement('FakeSegment', 'FakeSegment|a'),
                new SegmentElement('ZZZ', 'ZZZ|b'),
            ],
            [],
            0,
        );

        $this->assertSame(1, $next);
        $this->assertSame('FakeSegment|a', $segment->serialize(new Encoding()));
    }

    public function testParseSegmentsRejectsAnOffsetPastTheEndOfTheStream(): void
    {
        // A caller pointing past the stream's end is a logic error; failing loudly beats
        // silently parsing nothing and corrupting the caller's position bookkeeping.
        $segment = new FakeSegment();

        $this->expectException(OutOfBoundsException::class);

        $segment->parseSegments(new Encoding(), [], [], 0);
    }

    public function testSerializeLinesWrapsTheSerializedSegmentInAList(): void
    {
        // Groups splice their children's lines together; a segment contributes exactly one line,
        // letting groups serialize children uniformly without type-switching.
        $segment = new FakeSegment();
        $segment->parse(new Encoding(), 'FakeSegment|a');

        $this->assertSame(['FakeSegment|a'], $segment->serializeLines(new Encoding()));
    }

    public function testParseSegmentsSerializesTheRawLineIncludingTrailingEmpties(): void
    {
        // A bound segment has not been written, so serialize must emit the wire line. The object
        // path would trim the trailing empties and could not promise byte-exact output.
        $segment = $this->bindLine('FakeSegment|a||c||');

        $this->assertSame('FakeSegment|a||c||', $segment->serialize(new Encoding()));
    }

    public function testReadingABoundFieldKeepsTheRawLine(): void
    {
        // Callers read fields and then forward the message. Hydrating for the read must not
        // discard the raw line, or a read-only round trip would normalize trailing empties.
        $segment = $this->bindLine('FakeSegment|a~b||');

        $this->assertSame('b', $this->fieldValue($segment, 1, 1));
        $this->assertSame('FakeSegment|a~b||', $segment->serialize(new Encoding()));
    }

    public function testADifferentLineEndingDoesNotReencodeTheSegment(): void
    {
        // The line ending joins segments; it is not part of a segment line. Changing it must
        // leave a bound line untouched, including trailing empties the object path would drop.
        $segment = $this->bindLine('FakeSegment|a||');

        $this->assertSame('FakeSegment|a||', $segment->serialize(new Encoding(lineEnding: "\n")));
    }

    public function testADifferentFieldSeparatorReencodesTheSegment(): void
    {
        // The raw line is only valid for the delimiters it was parsed with. A different field
        // separator has to re-encode, and that object path trims trailing empties.
        $segment = $this->bindLine('FakeSegment|a^b||');

        $this->assertSame('FakeSegment#a^b', $segment->serialize(new Encoding(fieldSeparator: '#')));
        // Re-encoding for another delimiter must not consume the line.
        $this->assertSame('FakeSegment|a^b||', $segment->serialize(new Encoding()));
    }

    public function testWritingAFieldReencodesThatSegment(): void
    {
        // A write invalidates the raw line. The value just written has to appear, and the object
        // path trims trailing empties the raw line had been preserving.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition('Set ID', ST::class));
        $this->bindLine('FakeSegment|a||', $segment);

        $field = $segment->getFieldRepetition(1, 0);
        $this->assertInstanceOf(ST::class, $field);
        $this->assertSame('a', $field->getValue());
        $this->assertSame('FakeSegment|a||', $segment->serialize(new Encoding()));

        $field->setValue('z');

        $this->assertSame('FakeSegment|z', $segment->serialize(new Encoding()));
    }

    public function testClearingAFieldReencodesThatSegment(): void
    {
        // clear() is a write even though the new value is empty, so the raw line must die and
        // the object path must trim the segment down to its name.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition('Set ID', ST::class));
        $this->bindLine('FakeSegment|a||', $segment);

        $field = $segment->getFieldRepetition(1, 0);
        $this->assertInstanceOf(ST::class, $field);
        $field->clear();

        $this->assertSame('FakeSegment', $segment->serialize(new Encoding()));
    }

    public function testReplacingVariesDataReencodesThatSegment(): void
    {
        // setData() swaps the type of an extra subcomponent. That is a write, so the raw line
        // must die even though the copied characters are unchanged; trailing empties then trim.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition('Set ID', ST::class));
        $this->bindLine('FakeSegment|a&b||', $segment);

        $field = $segment->getFieldRepetition(1, 0);
        $this->assertInstanceOf(ST::class, $field);
        $varies = $field->getExtraComponents()->getComponent(0);
        $this->assertInstanceOf(Varies::class, $varies);

        $replacement = new ST();
        $varies->setData($replacement);

        $this->assertSame($replacement, $varies->getData());
        $this->assertSame('b', $replacement->getValue());
        $this->assertSame('FakeSegment|a&b', $segment->serialize(new Encoding()));
    }

    public function testReadingANestedTypedFieldKeepsTheRawLine(): void
    {
        // Typed accessors funnel through getFieldRepetition, including nested composites such as
        // a patient name. The read has to surface the wire value and still serialize the raw line.
        $pid = new PID();
        $pid->parseSegments(new Encoding(), [new SegmentElement('PID', 'PID|||||SMITH^JOHN')], [], 0);

        $name = $pid->getPatientName()[0];

        $this->assertSame('SMITH', $name->getFamilyName()->getSurname()->getValue());
        $this->assertSame('JOHN', $name->getGivenName()->getValue());
        $this->assertSame('PID|||||SMITH^JOHN', $pid->serialize(new Encoding()));
    }

    public function testSchemaQueriesDoNotParseFields(): void
    {
        // Names, counts, and repetition limits come from the definition table. Forcing a parse
        // there would build the field graph for callers that only inspect the schema, and
        // serialize of an unread segment must not parse either.
        $segment = new FakeHydratingSegment();
        $segment->add(new TypeDefinition('Set ID', isRequired: true, maxReps: 2));
        $segment->parseSegments(
            new Encoding(),
            [new SegmentElement('FakeHydratingSegment', 'FakeHydratingSegment|a||')],
            [],
            0,
        );

        $this->assertSame(['Set ID'], $segment->getNames());
        $this->assertSame(1, $segment->getFieldCount());
        $this->assertTrue($segment->isRequired(1));
        $this->assertSame(2, $segment->maxRepetitions(1));
        $this->assertSame(['FakeHydratingSegment'], $segment->firstNames());
        $this->assertSame('FakeHydratingSegment|a||', $segment->serialize(new Encoding()));
        $this->assertSame(0, $segment->hydrations);

        $segment->getField(1);
        $segment->getLength(1);

        $this->assertSame(1, $segment->hydrations);
    }

    private function bindLine(string $line, ?FakeSegment $segment = null): FakeSegment
    {
        $segment ??= new FakeSegment();
        $segment->parseSegments(new Encoding(), [new SegmentElement($segment->getName(), $line)], [], 0);

        return $segment;
    }
}
