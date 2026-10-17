<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidValue;
use RoundingWell\HL7\GenericComposite;
use RoundingWell\HL7\GenericPrimitive;
use RoundingWell\HL7\SegmentElement;
use RoundingWell\HL7\Tests\Fixtures\FakeCountingPrimitive;
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

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessageMatches('/repetition #3 .* only 2 repetitions are allowed/');

        $segment->getFieldRepetition(1, 2);
    }

    public function testFieldWithMaxRepsOneAcceptsExactlyOneRepetition(): void
    {
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition('Cap', maxReps: 1));
        $segment->getFieldRepetition(1, 0);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessageMatches('/repetition #2 .* only 1 repetition is allowed/');

        $segment->getFieldRepetition(1, 1);
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

    public function testParseInstantiatesNoFieldUntilOneIsRead(): void
    {
        // Parsing only keeps the line: callers who never read a field must not pay for building
        // it, and asking about the field table is not a read.
        FakeCountingPrimitive::reset();

        $segment = new FakeSegment();
        $segment->add(new TypeDefinition(type: FakeCountingPrimitive::class));
        $segment->add(new TypeDefinition(type: FakeCountingPrimitive::class));
        $segment->add(new TypeDefinition(type: FakeCountingPrimitive::class));
        $segment->parse(new Encoding(), 'FakeSegment|a|b|c');
        $segment->getFieldCount();
        $segment->getNames();

        $this->assertSame(0, FakeCountingPrimitive::$instantiations);

        $segment->getFieldRepetition(2, 0);

        $this->assertSame(1, FakeCountingPrimitive::$instantiations);
    }

    public function testGetFieldCountAndNamesIncludeWireFieldsBeyondTheDefinitions(): void
    {
        // Fields the schema does not declare still exist on the wire, so the field table reports
        // them (unnamed) before any of them has been read.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition('Set ID'));
        $segment->parse(new Encoding(), 'FakeSegment|1|b|c');

        $this->assertSame(3, $segment->getFieldCount());
        $this->assertSame(['Set ID', '', ''], $segment->getNames());
    }

    public function testMalformedFieldThrowsOnlyWhenThatFieldIsRead(): void
    {
        // A malformed value must not cost the caller the rest of the segment: parsing and reading
        // a neighbour succeed, and the bad field fails every time it is read.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition('Count', NM::class));
        $segment->parse(new Encoding(), 'FakeSegment|abc|ok');

        $this->assertSame('ok', $this->fieldValue($segment, 2, 0));

        foreach ([1, 2] as $attempt) {
            try {
                $segment->getFieldRepetition(1, 0);
                $this->fail("Read #{$attempt} of a malformed field must throw");
            } catch (InvalidValue $e) {
                $this->assertSame('Value of NM must be numeric, got abc', $e->getMessage());
            }
        }
    }

    public function testReparseReplacesThePreviousContent(): void
    {
        // Parsing a new line makes the segment that line; fields from the previous line must not
        // linger beyond the new line's end.
        $segment = new FakeSegment();
        $segment->parse(new Encoding(), 'FakeSegment|a|b|c');
        $segment->getFieldRepetition(3, 0);
        $segment->parse(new Encoding(), 'FakeSegment|x');

        $this->assertSame('x', $this->fieldValue($segment, 1, 0));
        $this->assertSame([], $segment->getField(3));
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

    public function testSerializeReturnsTheParsedLineVerbatim(): void
    {
        // Nothing was read or written, so the received line is the segment, trailing empties and all.
        $segment = new FakeSegment();
        $segment->parse(new Encoding(), 'FakeSegment|a||c||');

        $this->assertSame('FakeSegment|a||c||', $segment->serialize(new Encoding()));
    }

    public function testSerializeTrimsTrailingEmptyFieldsOfAHandBuiltSegment(): void
    {
        // A built segment has no received line to keep, so empty fields after the last value
        // collapse; touching a field without setting it must not emit a dangling separator.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition(type: ST::class));
        $this->stringField($segment, 1)->setValue('v');
        $segment->getFieldRepetition(3, 0);

        $this->assertSame('FakeSegment|v', $segment->serialize(new Encoding()));
    }

    public function testSerializeKeepsTheWireBytesOfFieldsReadButNotChanged(): void
    {
        // Reading is not writing: a literal "^" in a string field, trailing empty components and an
        // uninterpreted escape all come back exactly as received, even though re-encoding would
        // normalize each of them.
        $line = 'FakeSegment|a^b|A^B^|x\\X0D\\y||';

        $segment = new FakeSegment();
        $segment->add(new TypeDefinition(type: ST::class));
        $segment->parse(new Encoding(), $line);
        $segment->getFieldRepetition(1, 0);
        $segment->getFieldRepetition(2, 0);
        $segment->getFieldRepetition(3, 0);

        $this->assertSame($line, $segment->serialize(new Encoding()));
    }

    public function testSerializeReencodesOnlyTheWrittenField(): void
    {
        // A write changes the bytes of the written field and nothing else in the line.
        $segment = $this->segmentWithStringField3('FakeSegment|A^B^|keep^^|c||');
        $this->stringField($segment, 3)->setValue('NEW');

        $this->assertSame('FakeSegment|A^B^|keep^^|NEW||', $segment->serialize(new Encoding()));
    }

    public function testWritingBackTheOriginalValueRestoresTheOriginalBytes(): void
    {
        // Change is judged by content, not by whether a setter ran: undoing a write undoes its bytes.
        $segment = $this->segmentWithStringField3('FakeSegment|A^B^|keep^^|c||');
        $this->stringField($segment, 3)->setValue('NEW');
        $this->stringField($segment, 3)->setValue('c');

        $this->assertSame('FakeSegment|A^B^|keep^^|c||', $segment->serialize(new Encoding()));
    }

    public function testSerializeAppendsFieldsWrittenBeyondTheWire(): void
    {
        // A field set past the received ones is appended, with the gap kept by position.
        $segment = $this->segmentWithStringField3('FakeSegment|a');
        $this->stringField($segment, 3)->setValue('v');

        $this->assertSame('FakeSegment|a||v', $segment->serialize(new Encoding()));
    }

    public function testSerializeKeepsExtraWireFieldsWhenADeclaredFieldIsWritten(): void
    {
        // Vendor fields past the schema are carried as received when a declared field changes.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition(type: ST::class));
        $segment->parse(new Encoding(), 'FakeSegment|a|x^y^|z||');
        $this->stringField($segment, 1)->setValue('b');

        $this->assertSame('FakeSegment|b|x^y^|z||', $segment->serialize(new Encoding()));
    }

    public function testSerializeEmitsTheWireBytesOfAFieldThatFailedToParse(): void
    {
        // A field whose read threw was never parsed, so serializing carries its received bytes
        // instead of throwing again or dropping it.
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition('Count', NM::class));
        $segment->add(new TypeDefinition(type: ST::class));
        $segment->parse(new Encoding(), 'FakeSegment|abc|ok');

        try {
            $segment->getFieldRepetition(1, 0);
            $this->fail('Reading a malformed field must throw');
        } catch (InvalidValue $e) {
            // The field stays unparsed.
            $this->assertSame('Value of NM must be numeric, got abc', $e->getMessage());
        }

        $this->stringField($segment, 2)->setValue('v');

        $this->assertSame('FakeSegment|abc|v', $segment->serialize(new Encoding()));
    }

    public function testSerializeWithDifferentDelimitersReencodesEveryField(): void
    {
        // Received bytes are only valid under the delimiters they were written with; other
        // delimiters re-encode every field, and trailing empties are trimmed as before.
        $segment = new FakeSegment();
        $segment->parse(new Encoding(), 'FakeSegment|a^b||');

        $this->assertSame('FakeSegment|a@b', $segment->serialize(new Encoding(componentSeparator: '@')));
    }

    private function segmentWithStringField3(string $line): FakeSegment
    {
        $segment = new FakeSegment();
        $segment->add(new TypeDefinition());
        $segment->add(new TypeDefinition());
        $segment->add(new TypeDefinition(type: ST::class));
        $segment->parse(new Encoding(), $line);

        return $segment;
    }

    private function stringField(FakeSegment $segment, int $number): ST
    {
        $field = $segment->getFieldRepetition($number, 0);
        $this->assertInstanceOf(ST::class, $field);

        return $field;
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
}
