<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\XON;
use RoundingWell\HL7\Encoding;

#[CoversClass(XON::class)]
final class XONTest extends TestCase
{
    public function testFirstComponentMapsToTheOrganizationName(): void
    {
        // The organization name is the primary component of an organization reference.
        $xon = new XON();
        $xon->setRaw(new Encoding(), 'Cartoon Ducks Inc');

        $this->assertSame('Cartoon Ducks Inc', $xon->name->getValue());
    }
}
