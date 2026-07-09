<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\BaseField;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\Encoding;

#[CoversClass(BaseField::class)]
final class BaseFieldTest extends TestCase
{
    public function testExposesItsDefinitionMetadata(): void
    {
        // Callers rely on name/required/repeating to interpret and validate a segment.
        $field = new BaseField('Sequence Number', ST::class, required: true, repeating: true);

        $this->assertSame('Sequence Number', $field->getName());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->isRepeating());
    }

    public function testDefaultsToOptionalNonRepeating(): void
    {
        $field = new BaseField('Security', ST::class);

        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->isRepeating());
    }

    public function testNonRepeatingInstanceIsLazilyBuiltAndReused(): void
    {
        // A single instance is cached so repeated reads see the same populated value.
        $field = new BaseField('Security', ST::class);

        $instance = $field->getInstance();

        $this->assertInstanceOf(ST::class, $instance);
        $this->assertSame($instance, $field->getInstance());
    }

    public function testConstructorArgsAreForwardedToTheDataType(): void
    {
        // Field definitions constrain their type (e.g. MSH.1 is exactly one character).
        $field = new BaseField('Field Separator', ST::class, args: ['minLength' => 1, 'maxLength' => 1]);

        $instance = $field->getInstance();

        $this->assertInstanceOf(ST::class, $instance);
        $this->assertSame(1, $instance->maxLength);
    }

    public function testSetRawPopulatesTheSingleInstance(): void
    {
        $field = new BaseField('Security', ST::class);
        $field->setRaw(new Encoding(), 'SECRET');

        $instance = $field->getInstance();

        $this->assertInstanceOf(ST::class, $instance);
        $this->assertSame('SECRET', $instance->getValue());
    }

    public function testRepeatingFieldStartsEmpty(): void
    {
        // An unpopulated repeating field yields no instances, not a single blank one.
        $field = new BaseField('Character Set', ST::class, repeating: true);

        $this->assertSame([], $field->getInstance());
    }

    public function testSetRawSplitsRepetitionsIntoSeparateInstances(): void
    {
        // Repeating fields carry multiple values separated by the repetition character.
        $field = new BaseField('Character Set', ST::class, repeating: true);
        $field->setRaw(new Encoding(), 'ASCII~UNICODE~8859/1');

        $instances = $field->getInstance();

        $this->assertCount(3, $instances);
        $this->assertSame('ASCII', $instances[0]->getValue());
        $this->assertSame('UNICODE', $instances[1]->getValue());
        $this->assertSame('8859/1', $instances[2]->getValue());
    }
}
