<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\FNx;
use RoundingWell\HL7\Encoding;

#[CoversClass(FNx::class)]
final class FNxTest extends TestCase
{
    public function testFirstComponentMapsToTheSurname(): void
    {
        // The surname is the primary component of a family name.
        $fn = new FNx();
        $fn->setRaw(new Encoding(), 'DUCK');

        $this->assertSame('DUCK', $fn->surname->getValue());
    }
}
