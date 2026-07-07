<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\PT;
use RoundingWell\HL7\Encoding;

#[CoversClass(PT::class)]
final class PTTest extends TestCase
{
    public function testComponentsMapToProcessingIdAndMode(): void
    {
        // Processing id (e.g. P = production) and mode drive how a receiver treats the message.
        $pt = new PT();
        $pt->setRaw(new Encoding(), 'P^A');

        $this->assertSame('P', $pt->id->getValue());
        $this->assertSame('A', $pt->mode->getValue());
    }
}
