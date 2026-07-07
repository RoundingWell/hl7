<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\Encoding;

#[CoversClass(ID::class)]
final class IDTest extends TestCase
{
    public function testUnsetValueReportsNoValue(): void
    {
        // An unpopulated coded field must read as empty rather than error.
        $id = new ID(76);

        $this->assertFalse($id->hasValue());
        $this->assertSame('', $id->getValue());
        $this->assertSame('', (string) $id);
    }

    public function testSetRawDecodesAndStoresTheValue(): void
    {
        $id = new ID(76);
        $id->setRaw(new Encoding(), 'ADT');

        $this->assertTrue($id->hasValue());
        $this->assertSame('ADT', $id->getValue());
        $this->assertSame('ADT', (string) $id);
    }

    public function testSetRawIgnoresEmptyInput(): void
    {
        $id = new ID(76);
        $id->setRaw(new Encoding(), '');

        $this->assertFalse($id->hasValue());
    }
}
