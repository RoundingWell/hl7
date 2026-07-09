<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\Generic;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidField;
use RoundingWell\HL7\Field;
use RoundingWell\HL7\Segment;
use RoundingWell\HL7\Segment\MSH;

#[CoversClass(Segment::class)]
final class SegmentTest extends TestCase
{
    public function testExposesItsIdentifier(): void
    {
        $this->assertSame('PID', new Segment('PID')->getId());
    }

    public function testReturnsAnExplicitlyAddedField(): void
    {
        $segment = new Segment('PID');
        $field = new Field('Set ID', ST::class);
        $segment->addField(1, $field);

        $this->assertSame($field, $segment->getField(1));
    }

    public function testAddFieldRejectsFieldNumbersBelowOne(): void
    {
        // Field numbers are 1-based; a number below 1 cannot address any real field.
        $segment = new Segment('PID');

        $this->expectException(InvalidField::class);
        $this->expectExceptionMessageIsOrContains("Field 'PID.0' is too low");

        $segment->addField(0, new Field('Bad', ST::class));
    }

    public function testGenericSegmentSynthesizesUnknownFieldsAsGenericType(): void
    {
        // A segment with no schema still needs to expose arbitrary fields as raw strings.
        $segment = new Segment('ZZZ');

        $field = $segment->getField(3);

        $this->assertSame('Unknown', $field->getName());
        $this->assertInstanceOf(Generic::class, $field->getInstance());
    }

    public function testSetRawAssignsValuesByOneBasedFieldPosition(): void
    {
        // Field values arrive positionally, so index 0 must populate field number 1.
        $segment = new Segment('ZZZ');
        $segment->setRaw(new Encoding(), ['first', 'second']);

        $first = $segment->getField(1)->getInstance();
        $second = $segment->getField(2)->getInstance();

        $this->assertInstanceOf(Generic::class, $first);
        $this->assertInstanceOf(Generic::class, $second);

        $this->assertSame('first', $first->getPath('1'));
        $this->assertSame(['second'], $second->getValue());
    }

    public function testSchemaSegmentCreatesUnknownFieldsAsGeneric(): void
    {
        // A typed segment (MSH) must not silently invent fields outside its schema.
        $msh = new MSH();

        $field = $msh->getField(99);

        $this->assertInstanceOf(Generic::class, $field->getInstance());
    }
}
