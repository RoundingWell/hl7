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
    public function testUnsetValueReportsEmptyString(): void
    {
        // An unpopulated text value must read as empty rather than error.
        $tx = new TX();

        $this->assertSame('', $tx->getValue());
    }

    public function testParseDecodesAndStoresTheValue(): void
    {
        // Raw field data arrives escaped; the stored value must be the decoded text.
        $tx = new TX();
        $tx->parse(new Encoding(), 'A\\F\\B');

        $this->assertSame('A|B', $tx->getValue());
    }
}
