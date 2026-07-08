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
    public function testUnsetValueReportsNoValue(): void
    {
        // An unpopulated sequential numeric must read as empty rather than error.
        $snm = new SNM();

        $this->assertFalse($snm->hasValue());
        $this->assertSame('', $snm->getValue());
        $this->assertSame('', (string) $snm);
    }

    public function testSetRawStoresTheValue(): void
    {
        $snm = new SNM();
        $snm->setRaw(new Encoding(), '8005551212');

        $this->assertTrue($snm->hasValue());
        $this->assertSame('8005551212', $snm->getValue());
        $this->assertSame('8005551212', (string) $snm);
    }

    public function testSetRawIgnoresEmptyInput(): void
    {
        // An empty component is "absent", not a value that must satisfy the numeric rule.
        $snm = new SNM();
        $snm->setRaw(new Encoding(), '');

        $this->assertFalse($snm->hasValue());
    }

    public function testSetValueRejectsNonNumericValues(): void
    {
        // A sequential numeric may only hold digits; anything else is a malformed field.
        $snm = new SNM();

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessageIsOrContains('Value of SNM must be numeric, got 555-1212');

        $snm->setValue('555-1212');
    }
}
