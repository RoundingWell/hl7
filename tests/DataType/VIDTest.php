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
        $vid->parse(new Encoding(), '2.8');

        $this->assertSame('2.8', $vid->getId()->getValue());
    }

    public function testEveryComponentMapsToItsGetterInOrder(): void
    {
        // The trailing components are CE composites, so subcomponents use the "&" separator.
        $vid = new VID();
        $vid->parse(new Encoding(), '2.8^ENG&English&ISO639^FRA&French&ISO639');

        $this->assertSame('2.8', $vid->getId()->getValue());
        // Composite getters are exercised through a leaf getter on the returned CWE.
        $this->assertSame('ENG', $vid->getInternationalizationCode()->getIdentifier()->getValue());
        $this->assertSame('FRA', $vid->getInternationalVersion()->getIdentifier()->getValue());
    }
}
