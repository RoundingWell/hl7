<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\GenericMessage;
use RoundingWell\HL7\MessageDebugger;
use RoundingWell\HL7\Tests\Fixtures\FakeTypedGroupMessage;

#[CoversClass(MessageDebugger::class)]
final class MessageDebuggerTest extends TestCase
{
    private Encoding $encoding;

    protected function setUp(): void
    {
        $this->encoding = new Encoding("\r");
    }

    public function testDumpsTheMessageNameSegmentsAndPrimitiveFieldsIndentedTwoSpacesPerLevel(): void
    {
        // The dump is a nested tree: the message name at the root, each segment beneath it, each
        // populated field beneath its segment, indented two spaces per level. A primitive field
        // shows its access path, its schema name, and its value. This locks the exact shape callers
        // read, and proves the many empty MSH fields (MSH.3+) are dropped rather than printed.
        $message = new GenericMessage('ADT_A01', '2.4');
        $message->parse($this->encoding, 'MSH|^~\\&');

        $this->assertSame(
            implode("\n", [
                'ADT_A01',
                '  MSH',
                '    MSH.1 (Field Separator): |',
                '    MSH.2 (Encoding Characters): ^~\\&',
            ]),
            new MessageDebugger()->describe($message),
        );
    }

    public function testSkipsFieldsWhoseValueIsEmpty(): void
    {
        // A structure dump exists to find populated data, so empty elements are noise. An empty
        // primitive field (PID.2) and an empty composite field (PID.3, all components empty) must
        // both vanish, while the populated PID.1 around them stays.
        $message = new GenericMessage('ADT_A01', '2.4');
        $message->parse($this->encoding, "MSH|^~\\&\rPID|1||");

        $output = new MessageDebugger()->describe($message);

        $this->assertStringContainsString('PID.1 (Set ID): 1', $output);
        $this->assertStringNotContainsString('PID.2', $output);
        $this->assertStringNotContainsString('PID.3', $output);
    }

    public function testDescendsThroughCompositesToSubcomponentsShowingTheFullAccessPath(): void
    {
        // Full-depth descent: a composite field expands into its components, and a component that
        // is itself composite expands into its subcomponents, so the deepest leaf still carries a
        // complete dotted access path (PID.5.1.1) and its own value. Composite nodes that only
        // group children show a header with no value.
        $message = new GenericMessage('ADT_A01', '2.4');
        $message->parse($this->encoding, "MSH|^~\\&\rPID|1||||SMITH^JOHN");

        $output = new MessageDebugger()->describe($message);

        $this->assertStringContainsString("\n    PID.5 (Patient Name)\n", $output);
        $this->assertStringContainsString("\n      PID.5.1 (Family Name)\n", $output);
        $this->assertStringContainsString("\n        PID.5.1.1 (Surname): SMITH\n", $output);
        $this->assertStringContainsString("\n      PID.5.2 (Given Name): JOHN", $output);
    }

    public function testIndexesRepeatingFieldsOnlyWhenMoreThanOneRepetitionExists(): void
    {
        // A repeating field needs an index in its path to distinguish repetitions, but adding one
        // to the common single-repetition case would be noise. So the index appears only when there
        // is more than one repetition: bare "PID.3" is never emitted once PID.3 repeats.
        $message = new GenericMessage('ADT_A01', '2.4');
        $message->parse($this->encoding, "MSH|^~\\&\rPID|||12345~67890");

        $output = new MessageDebugger()->describe($message);

        $this->assertStringContainsString("\n    PID.3[0] (Patient Identifier List)\n", $output);
        $this->assertStringContainsString("\n    PID.3[1] (Patient Identifier List)\n", $output);
        $this->assertStringNotContainsString("\n    PID.3 (", $output);
    }

    public function testUnwrapsAVariesFieldToRenderTheValueItHolds(): void
    {
        // OBX-5 is declared as Varies: a wrapper that stands in for an as-yet-undetermined type.
        // The dump must look through the wrapper to the value it actually holds, reporting the
        // wrapper's field name and the underlying value rather than the wrapper itself.
        $message = new GenericMessage('ORU_R01', '2.4');
        $message->parse($this->encoding, "MSH|^~\\&\rOBX|1|ST|||HELLO");

        $output = new MessageDebugger()->describe($message);

        $this->assertStringContainsString("\n    OBX.5 (Observation Value): HELLO", $output);
    }

    public function testNestsASegmentInsideItsGroupSoTheHierarchyIsVisible(): void
    {
        // A group prints as a header with its contained structures indented beneath it, so a reader
        // can see where a segment landed in the message hierarchy rather than just as a flat list.
        $message = new FakeTypedGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rPID|1");

        $output = new MessageDebugger()->describe($message);

        $this->assertStringContainsString("\n  FakePatientGroup\n", $output);
        $this->assertStringContainsString("\n    PID\n", $output);
        $this->assertStringContainsString("\n      PID.1 (Set ID): 1", $output);
    }

    public function testRendersUndeclaredZSegmentFieldsWithoutASchema(): void
    {
        // An undeclared vendor (Z) segment is retained as a GenericSegment whose fields have no
        // schema, so all of their data lands in extra components. The dump must still surface it:
        // a lone anonymous component collapses onto the field line (ZPD.1), a multi-component field
        // expands into numbered components (ZPD.2), and repetitions are indexed (ZPD.3). No schema
        // name exists, so the "(name)" suffix is dropped rather than printed as empty parentheses.
        $message = new GenericMessage('ADT_A01', '2.4');
        $message->parse($this->encoding, "MSH|^~\\&\rZPD|foo|bar^baz|A~B");

        $this->assertSame(
            implode("\n", [
                'ADT_A01',
                '  MSH',
                '    MSH.1 (Field Separator): |',
                '    MSH.2 (Encoding Characters): ^~\\&',
                '  ZPD',
                '    ZPD.1: foo',
                '    ZPD.2',
                '      ZPD.2.1: bar',
                '      ZPD.2.2: baz',
                '    ZPD.3[0]: A',
                '    ZPD.3[1]: B',
            ]),
            new MessageDebugger()->describe($message),
        );
    }

    public function testDescendsIntoSubcomponentsOfUndeclaredFields(): void
    {
        // A generic field's parts can themselves carry subcomponents ("a&b"), so a single component
        // is only collapsed when it is a plain scalar; one that holds subcomponents keeps its own
        // level and expands beneath it. The value sits at subcomponent .1 with each following
        // subcomponent after it, and a leading empty subcomponent ("&d") is skipped so the surviving
        // part keeps its true position (.2) rather than silently shifting up.
        $message = new GenericMessage('ADT_A01', '2.4');
        $message->parse($this->encoding, "MSH|^~\\&\rZID|a&b|&d");

        $output = new MessageDebugger()->describe($message);

        $this->assertStringContainsString("\n    ZID.1\n", $output);
        $this->assertStringContainsString("\n      ZID.1.1\n", $output);
        $this->assertStringContainsString("\n        ZID.1.1.1: a\n", $output);
        $this->assertStringContainsString("\n        ZID.1.1.2: b\n", $output);
        $this->assertStringContainsString("\n        ZID.2.1.2: d", $output);
        $this->assertStringNotContainsString('ZID.2.1.1', $output);
    }

    public function testSurfacesExtraComponentsBeyondADeclaredFieldsSchema(): void
    {
        // Extra components exist to retain data a sender put beyond a field's declared schema. A dump
        // that dropped them would hide real message content, so they render as numbered children
        // after the declared value: PID.1 (a primitive) carries "1" plus an out-of-schema
        // subcomponent "x", which surface as PID.1.1 and PID.1.2.
        $message = new GenericMessage('ADT_A01', '2.4');
        $message->parse($this->encoding, "MSH|^~\\&\rPID|1&x");

        $output = new MessageDebugger()->describe($message);

        $this->assertStringContainsString("\n    PID.1 (Set ID)\n", $output);
        $this->assertStringContainsString("\n      PID.1.1: 1\n", $output);
        $this->assertStringContainsString("\n      PID.1.2: x", $output);
    }

    public function testOmitsAGroupAndSegmentThatHoldNoPopulatedFields(): void
    {
        // A structure that carries no populated fields contributes no lines, so its header is
        // suppressed rather than left dangling. Here the empty PID collapses, and because that was
        // the group's only content, the enclosing group collapses with it -- leaving only MSH.
        $message = new FakeTypedGroupMessage();
        $message->parse($this->encoding, "MSH|^~\\&\rPID");

        $output = new MessageDebugger()->describe($message);

        $this->assertStringContainsString("\n  MSH\n", $output);
        $this->assertStringNotContainsString('FakePatientGroup', $output);
        $this->assertStringNotContainsString('PID', $output);
    }
}
