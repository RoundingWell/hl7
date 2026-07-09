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
    public function testUnsetValueReportsEmptyString(): void
    {
        // An unpopulated user-defined coded field must read as empty rather than error.
        $is = new IS(363);

        $this->assertSame('', $is->getValue());
    }

    public function testParseDecodesAndStoresTheValue(): void
    {
        $is = new IS(363);
        $is->parse(new Encoding(), 'AccMgr');

        $this->assertSame('AccMgr', $is->getValue());
    }

    public function testTableIsRetained(): void
    {
        // The table backs value validation, so it must survive construction.
        $is = new IS(363);

        $this->assertSame(363, $is->getTable());
    }
}
