<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\TX;
use RoundingWell\HL7\Encoding;

#[CoversClass(TX::class)]
final class TXTest extends TestCase
{
    public function testUnsetValueReportsNoValue(): void
    {
        // An unpopulated text value must read as empty rather than error.
        $tx = new TX();

        $this->assertFalse($tx->hasValue());
        $this->assertSame('', $tx->getValue());
        $this->assertSame('', (string) $tx);
    }

    public function testSetRawDecodesAndStoresTheValue(): void
    {
        // Raw field data arrives escaped; the stored value must be the decoded text.
        $tx = new TX();
        $tx->setRaw(new Encoding(), 'A\\F\\B');

        $this->assertTrue($tx->hasValue());
        $this->assertSame('A|B', $tx->getValue());
        $this->assertSame('A|B', (string) $tx);
    }

    public function testSetRawIgnoresEmptyInput(): void
    {
        // An empty component is "absent", not a value to store.
        $tx = new TX();
        $tx->setRaw(new Encoding(), '');

        $this->assertFalse($tx->hasValue());
    }
}
