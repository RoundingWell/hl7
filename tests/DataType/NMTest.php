<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidValue;

#[CoversClass(NM::class)]
final class NMTest extends TestCase
{
    public function testUnsetValueReportsNoValue(): void
    {
        // An unpopulated numeric field must read as empty rather than error.
        $nm = new NM();

        $this->assertFalse($nm->hasValue());
        $this->assertSame('', $nm->getValue());
        $this->assertSame('', (string) $nm);
    }

    public function testSetRawStoresTheValue(): void
    {
        $nm = new NM();
        $nm->setRaw(new Encoding(), '42');

        $this->assertTrue($nm->hasValue());
        $this->assertSame('42', $nm->getValue());
        $this->assertSame('42', (string) $nm);
    }

    public function testSetRawIgnoresEmptyInput(): void
    {
        $nm = new NM();
        $nm->setRaw(new Encoding(), '');

        $this->assertFalse($nm->hasValue());
    }

    public function testSetValueRejectsValuesBelowTheMinimum(): void
    {
        $nm = new NM(min: 10);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessageIsOrContains('Value of NM must be greater than or equal to 10');

        $nm->setValue('5');
    }

    public function testSetValueRejectsValuesAboveTheMaximum(): void
    {
        $nm = new NM(max: 3);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessageIsOrContains('Value of NM must be less than or equal to 3');

        $nm->setValue('5');
    }
}
