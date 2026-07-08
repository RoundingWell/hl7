<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\Composite;
use RoundingWell\HL7\DataType\CX;
use RoundingWell\HL7\DataType\MSG;
use RoundingWell\HL7\DataType\PT;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidComponent;

#[CoversClass(Composite::class)]
final class CompositeTest extends TestCase
{
    public function testSetRawAssignsComponentsToTypedPropertiesInOrder(): void
    {
        // Component position determines meaning, so values must land on properties in declaration order.
        $msg = new MSG();
        $msg->setRaw(new Encoding(), 'ADT^A01^ADT_A01');

        $this->assertSame('ADT', $msg->messageType->getValue());
        $this->assertSame('A01', $msg->triggerEvent->getValue());
        $this->assertSame('ADT_A01', $msg->messageStructure->getValue());
    }

    public function testSetRawSplitsNestedCompositesOnTheSubcomponentSeparator(): void
    {
        // A composite nested as a component (CX.4 is an HD) carries its own components
        // as subcomponents, so it must split on '&' rather than swallowing the whole value.
        $cx = new CX();
        $cx->setRaw(new Encoding(), '10006579^^^Facility&1.2.840&ISO^MR');

        $this->assertSame('10006579', $cx->id->getValue());
        $this->assertSame('Facility', $cx->assigningAuthority->namespaceId->getValue());
        $this->assertSame('1.2.840', $cx->assigningAuthority->universalId->getValue());
        $this->assertSame('ISO', $cx->assigningAuthority->universalIdType->getValue());
        $this->assertSame('MR', $cx->identifierTypeCode->getValue());
    }

    public function testSetRawRejectsMoreComponentsThanTheTypeDeclares(): void
    {
        // A value with extra components signals a malformed or misunderstood field; fail loudly.
        $pt = new PT();

        $this->expectException(InvalidComponent::class);
        $this->expectExceptionMessageIsOrContains("Component 'PT.3' is not defined");

        $pt->setRaw(new Encoding(), 'P^A^EXTRA');
    }
}
