<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\SNM;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidValue;

#[CoversClass(SNM::class)]
final class SNMTest extends TestCase
{
    public function testUnsetValueReportsEmptyString(): void
    {
        // An unpopulated sequential numeric must read as empty rather than error.
        $snm = new SNM();

        $this->assertSame('', $snm->getValue());
    }

    public function testParseStoresTheValue(): void
    {
        $snm = new SNM();
        $snm->parse(new Encoding(), '8005551212');

        $this->assertSame('8005551212', $snm->getValue());
    }

    public function testSetValueRejectsNonNumericValues(): void
    {
        // A sequential numeric may only hold digits; anything else is a malformed field.
        $snm = new SNM();

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Value of SNM must be numeric, got 555-1212');

        $snm->setValue('555-1212');
    }
}
