<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\GenericComposite;
use RoundingWell\HL7\TypeDefinition;

#[CoversClass(TypeDefinition::class)]
final class TypeDefinitionTest extends TestCase
{
    public function testExposesTheConfigurationItWasGiven(): void
    {
        // A definition is a value object: every field it is constructed with must round-trip
        // unchanged, since segments and composites read them back to build fields.
        $definition = new TypeDefinition('Patient Name', ST::class, ['maxLength' => 20], true, 3);

        $this->assertSame('Patient Name', $definition->getName());
        $this->assertTrue($definition->isRequired());
        $this->assertSame(3, $definition->getMaxReps());
    }

    public function testDefaultsToAnOptionalUnboundedGenericCompositeField(): void
    {
        // The zero-argument defaults describe a schema-less field. A field sits above the
        // component level, so an undefined one is a GenericComposite -- preserving any component
        // structure -- rather than a flat Varies primitive. Not required, no repetition limit.
        $definition = new TypeDefinition();
        $instance = $definition->newInstance();

        $this->assertInstanceOf(GenericComposite::class, $instance);
    }

    public function testRejectsATypeThatDoesNotImplementType(): void
    {
        // A definition later instantiates its type as an HL7 value, so a non-Type class must be
        // refused at definition time rather than blowing up during parsing.
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/does not implement Type/');

        new TypeDefinition(type: \stdClass::class);
    }

    public function testRejectsANegativeRepetitionLimit(): void
    {
        // maxReps counts allowed repetitions; a negative limit is meaningless.
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/0 or greater/');

        new TypeDefinition(maxReps: -1);
    }

    public function testNewInstanceCreatesTheConfiguredType(): void
    {
        // newInstance is how fields are materialized, so it must return an instance of the
        // declared type.
        $definition = new TypeDefinition(type: ST::class);

        $this->assertInstanceOf(ST::class, $definition->newInstance());
    }

    public function testNewInstanceForwardsConstructorArguments(): void
    {
        // Constructor args (e.g. an HL7 table number) must reach the created instance so typed
        // fields are configured identically on every materialization.
        $definition = new TypeDefinition(type: ID::class, args: ['table' => 203]);
        $instance = $definition->newInstance();

        $this->assertInstanceOf(ID::class, $instance);
        $this->assertSame(203, $instance->getTable());
    }
}
