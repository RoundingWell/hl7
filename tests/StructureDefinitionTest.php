<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\Segment\NK1;
use RoundingWell\HL7\StructureDefinition;

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

    public function testRejectsATypeThatDoesNotImplementStructure(): void
    {
        // A definition later instantiates its type as part of a message, so a non-Structure class
        // must be refused at definition time rather than blowing up during assembly.
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/does not implement Structure/');

        new StructureDefinition(\stdClass::class);
    }
}
