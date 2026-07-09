<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\AbstractGroup;
use RoundingWell\HL7\AbstractMessage;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Group;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\StructureDefinition;
use RoundingWell\HL7\Tests\Fixtures\FakeGroupMessage;
use RoundingWell\HL7\Tests\Fixtures\FakeProcedure;

#[CoversClass(AbstractMessage::class)]
#[CoversClass(AbstractGroup::class)]
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

    public function testParseSkipsUnmatchedSegments(): void
    {
        // An unexpected/out-of-place segment is tolerated (decision 4A), never fatal.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rZZZ|junk\rNK1|1");

        $this->assertCount(1, $message->getAll('NK1'));
        $this->assertSame([], $message->getAll('ZZZ'));
    }

    public function testForeignSegmentInsideGroupDoesNotStrandItsMembers(): void
    {
        // A vendor Z-segment between a group's lead and a later member must be skipped without
        // dropping that member — otherwise real feeds silently lose data inside PROCEDURE/INSURANCE.
        $message = new FakeGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rPR1|1\rZZZ|junk\rROL|1");

        $procedures = $message->getAll('PROCEDURE');
        $this->assertCount(1, $procedures);
        $this->assertCount(1, $procedures[0]->getAll('ROL'));
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
        $this->assertCount(1, $message->getAll('PROCEDURE'));
        $this->assertCount(1, $message->getAll('PROCEDURE')[0]->getAll('PR1'));
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
}
