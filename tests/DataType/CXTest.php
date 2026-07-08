<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\CX;
use RoundingWell\HL7\Encoding;

#[CoversClass(CX::class)]
final class CXTest extends TestCase
{
    public function testComponentsMapIdAuthorityAndTypeCode(): void
    {
        // An identifier pairs its id with a (sub-componentized) assigning authority and a type code.
        $cx = new CX();
        $cx->setRaw(new Encoding(), '10006579^A^^AccMgr^MRN');

        $this->assertSame('10006579', $cx->id->getValue());
        $this->assertSame('A', $cx->identifierCheckDigit->getValue());
        $this->assertSame('AccMgr', $cx->assigningAuthority->namespaceId->getValue());
        $this->assertSame('MRN', $cx->identifierTypeCode->getValue());
    }
}
