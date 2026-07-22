<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\AbstractGroup;
use RoundingWell\HL7\AbstractMessage;
use RoundingWell\HL7\AcknowledgmentCode;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\GenericComposite;
use RoundingWell\HL7\GenericPrimitive;
use RoundingWell\HL7\GenericSegment;
use RoundingWell\HL7\Group;
use RoundingWell\HL7\IdGenerator;
use RoundingWell\HL7\Message\ACK;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\SegmentElement;
use RoundingWell\HL7\StructureDefinition;
use RoundingWell\HL7\Tests\Fixtures\FakeGroupMessage;
use RoundingWell\HL7\Tests\Fixtures\FakeProcedure;
use Symfony\Component\Clock\MockClock;

#[CoversClass(AbstractMessage::class)]
#[CoversClass(AbstractGroup::class)]
#[CoversClass(SegmentElement::class)]
final class AbstractMessageTest extends TestCase
{
    private Encoding $encoding;

    protected function setUp(): void
    {
        $this->encoding = new Encoding("\r");
    }

    public function testParsePopulatesRepeatingSegmentsInOrder(): void
    {
        // Every NK1 occurrence must be captured so no associated party is dropped.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rNK1|1\rNK1|2");

        $this->assertCount(2, $message->getAll('NK1'));
    }

    public function testParseIgnoresTrailingLineEnding(): void
    {
        // Messages commonly end with a trailing line ending; it must not become a phantom segment.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rNK1|1\r");

        $this->assertCount(1, $message->getAll('NK1'));
    }

    public function testParseLeavesOptionalSegmentAbsentWhenNotPresent(): void
    {
        // An absent optional segment must yield an empty list, not a lazily-created phantom.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rNK1|1");

        $this->assertSame([], $message->getAll('PV2'));
    }

    public function testParseEntersGroupOnLeadSegmentAndPopulatesMembers(): void
    {
        // A PROCEDURE group is entered only when its lead PR1 appears; its ROL members belong to it.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rPR1|1\rROL|1");

        $procedures = $message->getAll('PROCEDURE');
        $this->assertCount(1, $procedures);
        $procedure = $procedures[0];
        $this->assertInstanceOf(Group::class, $procedure);
        $this->assertCount(1, $procedure->getAll('PR1'));
        $this->assertCount(1, $procedure->getAll('ROL'));
    }

    public function testParseStartsANewGroupRepetitionOnEachLeadSegment(): void
    {
        // Two PR1 leads mean two distinct procedure repetitions, not one merged group.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rPR1|1\rPR1|2");

        $this->assertCount(2, $message->getAll('PROCEDURE'));
    }

    public function testRetainsUnmatchedForeignSegmentInPlace(): void
    {
        // A foreign segment the schema has no model for must not be silently dropped:
        // it is retained where it appeared so the round trip preserves input order, and
        // stays readable via getAll() the same way an undeclared Z-segment does.
        $data = "MSH|^~\\&\rQQQ|junk\rNK1|1";

        $message = new FakeGroupMessage();
        $message->parse($this->encoding, $data);

        $this->assertCount(1, $message->getAll('NK1'));
        $this->assertCount(1, $message->getAll('QQQ'));
        $this->assertSame($data, $message->serialize($this->encoding));
    }

    public function testRecoversRepeatedNonRepeatingSegmentAsReadableOccurrence(): void
    {
        // A second ZFA cannot match after its non-repeating slot closes. Rather than drop it or
        // hide it, parse preserves every occurrence: both round-trip and both are readable via
        // getAll. The non-repeating cap applies only to hand-building, not to received data.
        $data = "MSH|^~\\&\rZFA|1\rZFA|2";

        $message = new FakeGroupMessage();
        $message->parse($this->encoding, $data);

        $this->assertCount(2, $message->getAll('ZFA'));
        $this->assertSame($data, $message->serialize($this->encoding));
    }

    public function testRecoversDeclaredSegmentReappearingAfterItsSlot(): void
    {
        // PV2 reappearing after a later slot (ZFA) closed lands where the forward matcher cannot
        // place it. It is recovered as a second readable occurrence and round-trips in received
        // order rather than being hidden.
        $data = "MSH|^~\\&\rPV2|1\rZFA|1\rPV2|2";

        $message = new FakeGroupMessage();
        $message->parse($this->encoding, $data);

        $this->assertCount(2, $message->getAll('PV2'));
        $this->assertSame($data, $message->serialize($this->encoding));
    }

    public function testParseRetainsUndeclaredZSegmentInPlace(): void
    {
        // A vendor Z-segment must survive parse → serialize in its original position, so
        // typed messages can pass feeds through without losing site-defined data.
        $data = "MSH|^~\\&\rNK1|1\rZAA|custom\rNK1|2";

        $message = new FakeGroupMessage();
        $message->parse($this->encoding, $data);

        $this->assertSame($data, $message->serialize($this->encoding));
    }

    public function testRetainedZSegmentIsReadable(): void
    {
        // Retention is not just pass-through: site-defined fields must stay readable, the
        // same way GenericMessage exposes them.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rZAA|custom");

        $segments = $message->getAll('ZAA');
        $this->assertCount(1, $segments);
        $segment = $segments[0];
        $this->assertInstanceOf(GenericSegment::class, $segment);
        $field = $segment->getFieldRepetition(1, 0);
        $this->assertInstanceOf(GenericComposite::class, $field);
        $type = $field->getExtraComponents()->getComponent(0)->getData();
        $this->assertInstanceOf(GenericPrimitive::class, $type);
        $this->assertSame('custom', $type->getValue());
    }

    public function testZSegmentInsideGroupIsRetainedOnThatGroup(): void
    {
        // A vendor Z-segment between a group's lead and a later member must not strand the
        // member, and is retained on the group itself so byte order survives the round trip.
        $data = "MSH|^~\\&\rPR1|1\rZZZ|junk\rROL|1";

        $message = new FakeGroupMessage();
        $message->parse($this->encoding, $data);

        $procedures = $message->getAll('PROCEDURE');
        $this->assertCount(1, $procedures);
        $procedure = $procedures[0];
        $this->assertInstanceOf(Group::class, $procedure);
        $this->assertCount(1, $procedure->getAll('ROL'));
        $this->assertCount(1, $procedure->getAll('ZZZ'));
        $this->assertSame([], $message->getAll('ZZZ'));
        $this->assertSame($data, $message->serialize($this->encoding));
    }

    public function testSameZNameIsRetainedAtEachPosition(): void
    {
        // Each occurrence of a Z name splices back at its own position; grouping them
        // together would reorder the wire format.
        $data = "MSH|^~\\&\rZAA|1\rNK1|1\rZAA|2";

        $message = new FakeGroupMessage();
        $message->parse($this->encoding, $data);

        $this->assertCount(2, $message->getAll('ZAA'));
        $this->assertSame($data, $message->serialize($this->encoding));
    }

    public function testStructureAddedAfterParseStillSerializes(): void
    {
        // Retention must not freeze the parse-time order: a repetition added after parsing
        // (common when enriching a message before forwarding) must not be dropped. This is
        // the failure mode that ruled out recorded-parse-order serialization.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rNK1|1\rZAA|1");

        $message->getSegment('NK1', 1)->parse($this->encoding, 'NK1|2');

        $this->assertSame("MSH|^~\\&\rNK1|1\rZAA|1\rNK1|2", $message->serialize($this->encoding));
    }

    public function testHandBuiltMessageSerializesInCreationOrder(): void
    {
        // A never-parsed message has no wire order, so serialization follows creation order: PV2 is
        // built before NK1 here and therefore serializes first. Building in the desired output
        // order is the caller's responsibility.
        $message = new FakeGroupMessage();
        $message->getSegment('PV2', 0)->parse($this->encoding, 'PV2|1');
        $message->getSegment('NK1', 0)->parse($this->encoding, 'NK1|1');

        $this->assertSame("PV2|1\rNK1|1", $message->serialize($this->encoding));
    }

    public function testSerializeExpandsNestedGroupMembersInOrder(): void
    {
        // serializeLines() must recurse into a nested group (PROCEDURE) and splice its
        // members' lines into the walk; otherwise a group's segments would be silently dropped
        // from the serialized output instead of round-tripping.
        // The trailing "|" after the encoding characters is intentional and load-bearing: MSH::serialize
        // always emits a field separator after MSH.2, so the canonical serialized form of this
        // field-less MSH is "MSH|^~\&|". Dropping the pipe would break the round-trip assertion below.
        $data = "MSH|^~\\&\rPR1|1\rROL|1";
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, $data);

        $this->assertSame($data, $message->serialize($this->encoding));
    }

    public function testFirstNamesResolvesGroupLeadThroughNestedDefinition(): void
    {
        // FIRST-set of a group is its lead segment, computed without persisting a phantom group.
        $this->assertSame(['PR1'], new FakeProcedure()->firstNames());
    }

    public function testRequiredStructureAfterAGroupEndsThatGroupsScope(): void
    {
        // ZFA is required and declared after the repeating PROCEDURE group. Once PROCEDURE is
        // entered, its follow-set must include ZFA so that segment is recognized as ending
        // PROCEDURE's scope and returned to the parent, rather than being tolerated as foreign
        // input inside PROCEDURE (which would strand it there instead).
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rPR1|1\rROL|1\rZFA|1");

        // ZFA ended the group's scope and landed on the message...
        $this->assertCount(1, $message->getAll('ZFA'));
        // ...while the single PROCEDURE group still kept its own PR1 member.
        $procedures = $message->getAll('PROCEDURE');
        $this->assertCount(1, $procedures);
        $procedure = $procedures[0];
        $this->assertInstanceOf(Group::class, $procedure);
        $this->assertCount(1, $procedure->getAll('PR1'));
    }

    public function testGetRepetitionRejectsRepetitionAboveZeroForNonRepeatingStructure(): void
    {
        // PV2 is declared non-repeating; asking for a second occurrence must fail loudly rather
        // than silently fabricating a phantom segment parse() could never populate.
        $message = new FakeGroupMessage();

        $this->expectException(OutOfBoundsException::class);

        $message->getRepetition('PV2', 1);
    }

    public function testAddRejectsADuplicateStructureKey(): void
    {
        // Positional retention relies on every structure key being unique; re-registering a key
        // would silently overwrite the first definition and merge two positions into one slot,
        // so a duplicate key must fail loudly rather than corrupt the structure.
        $message = new FakeGroupMessage();

        $this->expectException(InvalidArgumentException::class);

        $message->add('MSH', new StructureDefinition(MSH::class));
    }

    public function testGetVersionReadsTheVersionIdFromMsh(): void
    {
        // getVersion() must reflect the version carried in the message's own MSH segment, since
        // callers rely on it to select version-specific parsing/validation rules.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, 'MSH|^~\\&|App|Fac|||202601011200||ORU^R01|1|P|2.8.1');

        $this->assertSame('2.8.1', $message->getVersion());
    }

    private function inboundMessage(): FakeGroupMessage
    {
        $message = new FakeGroupMessage();
        $message->parse(
            $this->encoding,
            'MSH|^~\\&|SendApp|SendFac|RecvApp^1.2.3^ISO|RecvFac|20240101120000||ADT^A01^ADT_A01|MSGCTRL1|P|2.5.1',
        );

        return $message;
    }

    private function fixedIdGenerator(string $id): IdGenerator
    {
        return new class($id) implements IdGenerator {
            public function __construct(
                private string $id,
            ) {}

            public function generate(): string
            {
                return $this->id;
            }
        };
    }

    public function testGenerateAckSwapsSenderAndReceiver(): void
    {
        // The acknowledgment must route back to the original sender, so the outbound
        // sending app/facility come from the inbound receiving app/facility and vice versa.
        $ack = $this->inboundMessage()->generateACK(
            AcknowledgmentCode::AA,
            new MockClock('2024-02-02 10:00:00', '+00:00'),
            $this->fixedIdGenerator('ACK-ID-1'),
        );

        $this->assertInstanceOf(ACK::class, $ack);
        $msh = $ack->getMSH();
        $this->assertSame('RecvApp', $msh->getSendingApplication()->getNamespaceId()->getValue());
        $this->assertSame('RecvFac', $msh->getSendingFacility()->getNamespaceId()->getValue());
        $this->assertSame('SendApp', $msh->getReceivingApplication()->getNamespaceId()->getValue());
        $this->assertSame('SendFac', $msh->getReceivingFacility()->getNamespaceId()->getValue());

        // The inbound receiving application carries all three HD components; copyType must
        // recurse across every component of the composite, not just the first.
        $this->assertSame('1.2.3', $msh->getSendingApplication()->getUniversalId()->getValue());
        $this->assertSame('ISO', $msh->getSendingApplication()->getUniversalIdType()->getValue());
    }

    public function testGenerateAckStampsTimestampAndFreshControlId(): void
    {
        // MSH-7 comes from the injected clock; MSH-10 is a NEW id for the ACK itself
        // (the ACK is its own message), while MSA-2 echoes the request's control id so
        // the original sender can correlate the acknowledgment to what it sent.
        $ack = $this->inboundMessage()->generateACK(
            AcknowledgmentCode::AA,
            new MockClock('2024-02-02 10:00:00', '+00:00'),
            $this->fixedIdGenerator('ACK-ID-1'),
        );

        $this->assertInstanceOf(ACK::class, $ack);
        $this->assertSame('20240202100000+0000', $ack->getMSH()->getDateTimeOfMessage()->getValue());
        $this->assertSame('ACK-ID-1', $ack->getMSH()->getMessageControlId()->getValue());
        $this->assertSame('MSGCTRL1', $ack->getMSA()->getMessageControlId()->getValue());
    }

    public function testGenerateAckSetsHeaderTypeProcessingIdAndVersion(): void
    {
        // MSH-9 must identify the reply as an ACK carrying the inbound trigger event;
        // processing id and version are echoed so the reply matches the request's context.
        $ack = $this->inboundMessage()->generateACK(
            AcknowledgmentCode::AA,
            new MockClock('2024-02-02 10:00:00', '+00:00'),
            $this->fixedIdGenerator('ACK-ID-1'),
        );

        $this->assertInstanceOf(ACK::class, $ack);
        $msh = $ack->getMSH();
        $this->assertSame('ACK', $msh->getMessageType()->getMessageType()->getValue());
        $this->assertSame('A01', $msh->getMessageType()->getTriggerEvent()->getValue());
        $this->assertSame('ACK', $msh->getMessageType()->getMessageStructure()->getValue());
        $this->assertSame('|', $msh->getFieldSeparator()->getValue());
        $this->assertSame('^~\\&', $msh->getEncodingCharacters()->getValue());
        $this->assertSame('P', $msh->getProcessingId()->getId()->getValue());
        $this->assertSame('2.5.1', $msh->getVersionId()->getId()->getValue());
    }

    /**
     * @return list<array{AcknowledgmentCode, string}>
     */
    public static function acknowledgmentCodeProvider(): array
    {
        return [
            [AcknowledgmentCode::AA, 'AA'],
            [AcknowledgmentCode::AE, 'AE'],
            [AcknowledgmentCode::AR, 'AR'],
        ];
    }

    #[DataProvider('acknowledgmentCodeProvider')]
    public function testGenerateAckWritesTheAcknowledgmentCodeToMsa1(AcknowledgmentCode $code, string $expected): void
    {
        // The ack code parameter is the whole point of choosing accept/error/reject; it must
        // land in MSA-1 exactly, for every supported code.
        $ack = $this->inboundMessage()->generateACK(
            $code,
            new MockClock('2024-02-02 10:00:00', '+00:00'),
            $this->fixedIdGenerator('ACK-ID-1'),
        );

        $this->assertInstanceOf(ACK::class, $ack);
        $this->assertSame($expected, $ack->getMSA()->getAcknowledgmentCode()->getValue());
    }

    public function testRecoversOutOfOrderDeclaredSegmentAsReadableRepetition(): void
    {
        // NK1 is declared before the required ZFA. Arriving after ZFA it can no longer match the
        // forward pointer, but as a declared repeating segment it must be recovered into its own slot
        // so getAll('NK1') sees it, and it must round-trip in received order.
        $data = "MSH|^~\\&\rZFA|1\rNK1|1";

        $message = new FakeGroupMessage();
        $message->parse($this->encoding, $data);

        $this->assertCount(1, $message->getAll('NK1'));
        $this->assertSame($data, $message->serialize($this->encoding));
    }

    public function testOutOfOrderGroupLeadFallsBackToGenericSegment(): void
    {
        // PR1 leads the PROCEDURE group. Arriving out of order (after ZFA) it is NOT re-run as a group
        // — group consumption out of place is unsafe — but it must still round-trip, kept as a generic
        // segment readable under its own name rather than as a PROCEDURE.
        $data = "MSH|^~\\&\rZFA|1\rPR1|1";

        $message = new FakeGroupMessage();
        $message->parse($this->encoding, $data);

        $this->assertSame([], $message->getAll('PROCEDURE'));
        $this->assertCount(1, $message->getAll('PR1'));
        $this->assertInstanceOf(GenericSegment::class, $message->getAll('PR1')[0]);
        $this->assertSame($data, $message->serialize($this->encoding));
    }

    public function testOutOfOrderSecondMshIsRecoveredTypedAndRoundTripsByteIdentical(): void
    {
        // MSH is non-repeating and declared first, so a second MSH arriving after ZFA can no
        // longer match the forward pointer and must be recovered. It must come back as a typed
        // MSH (via recoverSegment's typed-recovery path), not a GenericSegment: MSH.parse/serialize
        // treat MSH.2 (the encoding characters, e.g. "^~\&") as a verbatim value, while
        // GenericSegment would run it through the generic field parser, splitting it on its own
        // component/subcomponent separators and re-escaping its lone backslash — corrupting the
        // byte-for-byte round trip this test guards against.
        $data = "MSH|^~\\&|App1\rZFA|1\rMSH|^~\\&|App2";

        $message = new FakeGroupMessage();
        $message->parse($this->encoding, $data);

        $mshSegments = $message->getAll('MSH');

        $this->assertCount(2, $mshSegments);
        $this->assertInstanceOf(MSH::class, $mshSegments[0]);
        $this->assertInstanceOf(MSH::class, $mshSegments[1]);
        $this->assertSame($data, $message->serialize($this->encoding));
    }

    public function testGetRepetitionRejectsNonContiguousRepetitionForRepeatingStructure(): void
    {
        // NK1 is repeating but has zero occurrences here; asking for repetition #3 must fail
        // loudly rather than silently fabricating a phantom gap-filling repetition that parse()
        // never populated and that would sit at the wrong index relative to any later NK1s.
        $message = new FakeGroupMessage();

        $this->expectException(OutOfBoundsException::class);

        $message->getRepetition('NK1', 3);
    }

    public function testGetStructuresReturnsMaterializedChildrenInAppearanceOrder(): void
    {
        // getStructures exposes the ordered materialized children the by-name accessors cannot:
        // a debugger (or any consumer) needs to walk the message as it was received, so the two
        // NK1 repetitions must come back after MSH, in the order they were parsed.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rNK1|1\rNK1|2");

        $names = array_map(static fn($structure): string => $structure->getName(), $message->getStructures());

        $this->assertSame(['MSH', 'NK1', 'NK1'], $names);
    }

    public function testDebugRendersTheMessageStructureAsAnIndentedString(): void
    {
        // debug() is the message-level entry point to the structure dump; it must delegate to the
        // renderer and return the indented tree so callers can print it without wiring up the
        // debugger themselves.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, 'MSH|^~\\&');

        $this->assertSame(
            "FakeGroupMessage\n  MSH\n    MSH.1 (Field Separator): |\n    MSH.2 (Encoding Characters): ^~\\&",
            $message->debug(),
        );
    }
}
