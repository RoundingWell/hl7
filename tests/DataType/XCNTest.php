<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\XCN;
use RoundingWell\HL7\Encoding;

#[CoversClass(XCN::class)]
final class XCNTest extends TestCase
{
    public function testComponentsMapIdAndNestedFamilyName(): void
    {
        // A person reference pairs an id with a (sub-componentized) family name.
        $xcn = new XCN();
        $xcn->setRaw(new Encoding(), '37^DISNEY^WALT');

        $this->assertSame('37', $xcn->id->getValue());
        $this->assertSame('DISNEY', $xcn->familyName->surname->getValue());
        $this->assertSame('WALT', $xcn->givenName->getValue());
    }
}
