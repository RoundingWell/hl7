<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\HasComponents;
use RoundingWell\HL7\DataType\MSG;
use RoundingWell\HL7\DataType\PT;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidComponent;

#[CoversTrait(HasComponents::class)]
final class HasComponentsTest extends TestCase
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

    public function testSetRawRejectsMoreComponentsThanTheTypeDeclares(): void
    {
        // A value with extra components signals a malformed or misunderstood field; fail loudly.
        $pt = new PT();

        $this->expectException(InvalidComponent::class);
        $this->expectExceptionMessageIsOrContains("Component 'PT.3' is not defined");

        $pt->setRaw(new Encoding(), 'P^A^EXTRA');
    }
}
