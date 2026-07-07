<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\IS;
use RoundingWell\HL7\Encoding;

#[CoversClass(IS::class)]
final class ISTest extends TestCase
{
    public function testUnsetValueReportsNoValue(): void
    {
        // An unpopulated user-defined coded field must read as empty rather than error.
        $is = new IS(363);

        $this->assertFalse($is->hasValue());
        $this->assertSame('', $is->getValue());
        $this->assertSame('', (string) $is);
    }

    public function testSetRawDecodesAndStoresTheValue(): void
    {
        $is = new IS(363);
        $is->setRaw(new Encoding(), 'AccMgr');

        $this->assertTrue($is->hasValue());
        $this->assertSame('AccMgr', $is->getValue());
        $this->assertSame('AccMgr', (string) $is);
    }

    public function testSetRawIgnoresEmptyInput(): void
    {
        $is = new IS(363);
        $is->setRaw(new Encoding(), '');

        $this->assertFalse($is->hasValue());
    }
}
