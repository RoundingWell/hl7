<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\BaseField;
use RoundingWell\HL7\BaseSegment;
use RoundingWell\HL7\DataType\Generic;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidField;
use RoundingWell\HL7\Segment\MSH;

#[CoversClass(BaseSegment::class)]
final class BaseSegmentTest extends TestCase
{
    public function testExposesItsIdentifier(): void
    {
        $this->assertSame('PID', new BaseSegment('PID')->getId());
    }

    public function testReturnsAnExplicitlyAddedField(): void
    {
        $segment = new BaseSegment('PID');
        $field = new BaseField('Set ID', ST::class);
        $segment->addField(1, $field);

        $this->assertSame($field, $segment->getField(1));
    }

    public function testAddFieldRejectsFieldNumbersBelowOne(): void
    {
        // Field numbers are 1-based; a number below 1 cannot address any real field.
        $segment = new BaseSegment('PID');

        $this->expectException(InvalidField::class);
        $this->expectExceptionMessageIsOrContains("Field 'PID.0' is too low");

        $segment->addField(0, new BaseField('Bad', ST::class));
    }

    public function testGenericSegmentSynthesizesUnknownFieldsAsGenericType(): void
    {
        // A segment with no schema still needs to expose arbitrary fields as raw strings.
        $segment = new BaseSegment('ZZZ');

        $field = $segment->getField(3);

        $this->assertSame('Unknown', $field->getName());
        $this->assertInstanceOf(Generic::class, $field->getInstance());
    }

    public function testSetRawAssignsValuesByOneBasedFieldPosition(): void
    {
        // Field values arrive positionally, so index 0 must populate field number 1.
        $segment = new BaseSegment('ZZZ');
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
