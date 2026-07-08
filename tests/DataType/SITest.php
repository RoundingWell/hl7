<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\SI;
use RoundingWell\HL7\Encoding;

#[CoversClass(SI::class)]
final class SITest extends TestCase
{
    public function testUnsetValueReportsNoValue(): void
    {
        // An unpopulated sequence identifier must read as empty rather than error.
        $si = new SI();

        $this->assertFalse($si->hasValue());
        $this->assertSame('', $si->getValue());
        $this->assertSame('', (string) $si);
    }

    public function testSetRawDecodesAndStoresTheValue(): void
    {
        // Raw field data arrives escaped; the stored value must be the decoded text.
        $si = new SI();
        $si->setRaw(new Encoding(), 'A\\F\\B');

        $this->assertTrue($si->hasValue());
        $this->assertSame('A|B', $si->getValue());
        $this->assertSame('A|B', (string) $si);
    }

    public function testSetRawIgnoresEmptyInput(): void
    {
        // An empty component is "absent", not a value to store.
        $si = new SI();
        $si->setRaw(new Encoding(), '');

        $this->assertFalse($si->hasValue());
    }
}
