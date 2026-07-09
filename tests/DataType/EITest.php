<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\EI;
use RoundingWell\HL7\Encoding;

#[CoversClass(EI::class)]
final class EITest extends TestCase
{
    public function testComponentsMapToEntityIdentifierParts(): void
    {
        // Entity identifiers pair a local id with a namespace, so both must land correctly.
        $ei = new EI();
        $ei->parse(new Encoding(), 'ENTITY^AccMgr^1.2.3^ISO');

        $this->assertSame('ENTITY', $ei->getId()->getValue());
        $this->assertSame('AccMgr', $ei->getNamespaceId()->getValue());
        $this->assertSame('1.2.3', $ei->getUniversalId()->getValue());
        $this->assertSame('ISO', $ei->getUniversalIdType()->getValue());
    }
}
