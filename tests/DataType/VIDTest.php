<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\VID;
use RoundingWell\HL7\Encoding;

#[CoversClass(VID::class)]
final class VIDTest extends TestCase
{
    public function testFirstComponentMapsToTheVersionId(): void
    {
        // The version id selects which HL7 grammar applies to the message.
        $vid = new VID();
        $vid->setRaw(new Encoding(), '2.8');

        $this->assertSame('2.8', $vid->id->getValue());
    }
}
