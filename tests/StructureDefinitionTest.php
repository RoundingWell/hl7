<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\Segment\NK1;
use RoundingWell\HL7\StructureDefinition;
use RoundingWell\HL7\Tests\Fixtures\FakeStructure;

#[CoversClass(StructureDefinition::class)]
final class StructureDefinitionTest extends TestCase
{
    public function testExposesTheConfigurationItWasGiven(): void
    {
        // A definition is a value object: every field it is constructed with must round-trip
        // unchanged, since messages read them back to assemble their segments and groups.
        $definition = new StructureDefinition(MSH::class, isRequired: true, isRepeating: false);

        $this->assertTrue($definition->isRequired());
        $this->assertFalse($definition->isRepeating());
    }

    public function testDefaultsToAnOptionalNonRepeatingStructureWithNoArguments(): void
    {
        // Only the type is required; the remaining defaults describe the least-constrained
        // structure: no constructor args, not required, and not repeating.
        $definition = new StructureDefinition(NK1::class);

        $this->assertFalse($definition->isRequired());
        $this->assertFalse($definition->isRepeating());
        $this->assertFalse($definition->isGroup());
    }

    public function testNewInstanceCreatesTheConfiguredType(): void
    {
        // newInstance is how a message materializes its parts, so it must return an instance of
        // the declared type.
        $definition = new StructureDefinition(MSH::class);
        $instance = $definition->newInstance();

        $this->assertInstanceOf(MSH::class, $instance);
    }

    public function testRejectsATypeThatIsNotAStructure(): void
    {
        // A definition later instantiates its type as part of a message, so an unparseable class
        // must be refused at definition time rather than blowing up during assembly.
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/must be a Segment or extend AbstractGroup/');

        new StructureDefinition(\stdClass::class);
    }

    public function testRejectsAStructureThatIsNeitherASegmentNorAGroup(): void
    {
        // Parsing dispatches on exactly two cases: a Segment consumes a line, an AbstractGroup
        // recurses. A bare Structure could be registered but never parsed, so it must be refused
        // up front instead of failing in the middle of a message.
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/must be a Segment or extend AbstractGroup/');

        new StructureDefinition(FakeStructure::class);
    }
}
