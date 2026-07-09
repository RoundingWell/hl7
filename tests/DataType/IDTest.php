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
    public function testUnsetValueReportsEmptyString(): void
    {
        // An unpopulated coded field must read as empty rather than error.
        $id = new ID(76);

        $this->assertSame('', $id->getValue());
    }

    public function testParseDecodesAndStoresTheValue(): void
    {
        $id = new ID(76);
        $id->parse(new Encoding(), 'ADT');

        $this->assertSame('ADT', $id->getValue());
    }

    public function testTableIsRetained(): void
    {
        // The table backs value validation, so it must survive construction.
        $id = new ID(76);

        $this->assertSame(76, $id->getTable());
    }
}
