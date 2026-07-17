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
    public function testUnsetValueReportsEmptyString(): void
    {
        // An unpopulated numeric field must read as empty rather than error.
        $nm = new NM();

        $this->assertSame('', $nm->getValue());
    }

    public function testParseStoresTheValue(): void
    {
        $nm = new NM();
        $nm->parse(new Encoding(), '42');

        $this->assertSame('42', $nm->getValue());
    }

    public function testBoundsAreRetained(): void
    {
        // The bounds back value validation, so they must survive construction.
        $nm = new NM(min: 1, max: 100);

        $this->assertSame(1, $nm->getMin());
        $this->assertSame(100, $nm->getMax());
    }

    public function testSetValueRejectsNonNumericValues(): void
    {
        // A numeric field may only hold numbers; anything else is a malformed field.
        $nm = new NM();

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Value of NM must be numeric, got abc');

        $nm->setValue('abc');
    }

    public function testSetValueRejectsValuesBelowTheMinimum(): void
    {
        $nm = new NM(min: 10);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Value of NM must be greater than or equal to 10');

        $nm->setValue('5');
    }

    public function testSetValueRejectsValuesAboveTheMaximum(): void
    {
        $nm = new NM(max: 3);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Value of NM must be less than or equal to 3');

        $nm->setValue('5');
    }
}
