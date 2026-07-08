<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\Varies;
use RoundingWell\HL7\Encoding;

#[CoversClass(Varies::class)]
final class VariesTest extends TestCase
{
    public function testUnsetValueReportsNoValue(): void
    {
        // An unpopulated variable datatype must read as empty rather than error.
        $varies = new Varies();

        $this->assertFalse($varies->hasValue());
        $this->assertSame('', $varies->getValue());
        $this->assertSame('', (string) $varies);
    }

    public function testSetRawDecodesAndStoresTheValue(): void
    {
        // Raw field data arrives escaped; the stored value must be the decoded text.
        $varies = new Varies();
        $varies->setRaw(new Encoding(), 'A\\F\\B');

        $this->assertTrue($varies->hasValue());
        $this->assertSame('A|B', $varies->getValue());
        $this->assertSame('A|B', (string) $varies);
    }

    public function testSetRawIgnoresEmptyInput(): void
    {
        // An empty component is "absent", not a value to store.
        $varies = new Varies();
        $varies->setRaw(new Encoding(), '');

        $this->assertFalse($varies->hasValue());
    }
}
