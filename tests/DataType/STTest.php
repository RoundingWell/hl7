<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidValue;

#[CoversClass(ST::class)]
final class STTest extends TestCase
{
    public function testUnsetValueReportsNoValue(): void
    {
        // An unpopulated field must be distinguishable from one holding an empty string.
        $st = new ST();

        $this->assertFalse($st->hasValue());
        $this->assertSame('', $st->getValue());
        $this->assertSame('', (string) $st);
    }

    public function testSetRawDecodesAndStoresTheValue(): void
    {
        // Raw field data arrives escaped; the stored value must be the decoded text.
        $st = new ST();
        $st->setRaw(new Encoding(), 'A\\F\\B');

        $this->assertTrue($st->hasValue());
        $this->assertSame('A|B', $st->getValue());
        $this->assertSame('A|B', (string) $st);
    }

    public function testSetRawIgnoresEmptyInputSoTheFieldStaysUnset(): void
    {
        // An empty component is "absent", not a value that must satisfy minLength.
        $st = new ST(minLength: 3);
        $st->setRaw(new Encoding(), '');

        $this->assertFalse($st->hasValue());
    }

    public function testSetValueRejectsValuesShorterThanMinLength(): void
    {
        $st = new ST(minLength: 3);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessageIsOrContains('Value of ST must be at least 3 characters long');

        $st->setValue('ab');
    }

    public function testSetValueRejectsValuesLongerThanMaxLength(): void
    {
        $st = new ST(maxLength: 2);

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessageIsOrContains('Value of ST must be at most 2 characters long');

        $st->setValue('abc');
    }

    public function testSetValueAcceptsValuesWithinBounds(): void
    {
        $st = new ST(minLength: 1, maxLength: 5);
        $st->setValue('abc');

        $this->assertSame('abc', $st->getValue());
    }
}
