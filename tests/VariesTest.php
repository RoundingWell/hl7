<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\ExtraComponents;
use RoundingWell\HL7\GenericPrimitive;
use RoundingWell\HL7\Primitive;
use RoundingWell\HL7\Type;
use RoundingWell\HL7\Varies;

#[CoversClass(Varies::class)]
final class VariesTest extends TestCase
{
    public function testDefaultDataIsAGenericPrimitive(): void
    {
        // Varies stands in for an as-yet-undetermined type: until setData() picks a
        // concrete type it must default to the generic primitive so unknown values
        // can still be read and written.
        $varies = new Varies();

        $this->assertInstanceOf(GenericPrimitive::class, $varies->getData());
    }

    public function testGetNameDelegatesToTheUnderlyingData(): void
    {
        // Varies is a pass-through wrapper: its name is whatever its data reports,
        // never a name of its own.
        $varies = new Varies();

        $this->assertSame($varies->getData()->getName(), $varies->getName());
    }

    public function testParseDelegatesToTheUnderlyingData(): void
    {
        // Parsing must feed the wrapped type so the decoded value lands on the data,
        // not on some field held by the wrapper.
        $varies = new Varies();
        $varies->parse(new Encoding(), 'HELLO');

        $data = $varies->getData();
        $this->assertInstanceOf(Primitive::class, $data);
        $this->assertSame('HELLO', $data->getValue());
    }

    public function testClearDelegatesToTheUnderlyingData(): void
    {
        // Clearing the wrapper must reset the wrapped value so a reused instance
        // cannot leak a prior value through getData().
        $varies = new Varies();
        $varies->parse(new Encoding(), 'HELLO');
        $varies->clear();

        $data = $varies->getData();
        $this->assertInstanceOf(Primitive::class, $data);
        $this->assertSame('', $data->getValue());
    }

    public function testGetExtraComponentsReturnsTheUnderlyingCollection(): void
    {
        // Extra components live on the wrapped type; the wrapper must expose that same
        // collection rather than a copy, or components written through the data would
        // be invisible through the wrapper.
        $varies = new Varies();

        $this->assertSame($varies->getData()->getExtraComponents(), $varies->getExtraComponents());
    }

    public function testSetDataAdoptsThePrimitiveAndCopiesTheExistingValue(): void
    {
        // Swapping in a concrete primitive must carry the value already parsed into
        // the wrapper across to the new type, and discard whatever value the
        // replacement arrived with, so no data is lost or leaked during the swap.
        $varies = new Varies();
        $varies->parse(new Encoding(), 'HELLO');

        $replacement = new class extends GenericPrimitive {
            #[\Override]
            public function getName(): string
            {
                return 'REPLACED';
            }
        };
        $replacement->setValue('PRESET');

        $varies->setData($replacement);

        $this->assertSame($replacement, $varies->getData());
        $this->assertSame('HELLO', $replacement->getValue());
        $this->assertSame('REPLACED', $varies->getName());
    }

    public function testSetDataRejectsNonPrimitiveData(): void
    {
        // Copying the existing value into a new type is only defined for primitives.
        // A composite replacement is not yet supported, so it must fail loudly rather
        // than silently drop the existing value.
        $varies = new Varies();

        $nonPrimitive = new class implements Type {
            #[\Override]
            public function getName(): string
            {
                return 'COMPOSITE';
            }

            #[\Override]
            public function getExtraComponents(): ExtraComponents
            {
                return new ExtraComponents();
            }

            #[\Override]
            public function clear(): void {}

            #[\Override]
            public function parse(Encoding $encoding, string $data): void {}
        };

        $this->expectException(Exception::class);
        $this->expectExceptionMessageIsOrContains('Cannot set data on ' . GenericPrimitive::class);

        $varies->setData($nonPrimitive);
    }
}
