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
    public function testUnsetValueReportsEmptyString(): void
    {
        // An unpopulated sequence identifier must read as empty rather than error.
        $si = new SI();

        $this->assertSame('', $si->getValue());
    }

    public function testParseDecodesAndStoresTheValue(): void
    {
        // Raw field data arrives escaped; the stored value must be the decoded text.
        $si = new SI();
        $si->parse(new Encoding(), 'A\\F\\B');

        $this->assertSame('A|B', $si->getValue());
    }
}
