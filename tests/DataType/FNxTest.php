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
        $fn->parse(new Encoding(), 'DUCK');

        $this->assertSame('DUCK', $fn->getSurname()->getValue());
    }

    public function testEveryComponentMapsToItsGetter(): void
    {
        // Every component is a plain ST, so each maps directly to its own getter.
        $fn = new FNx();
        $fn->parse(new Encoding(), 'DUCK^de^Duck^van^Vandenberg');

        $this->assertSame('DUCK', $fn->getSurname()->getValue());
        $this->assertSame('de', $fn->getOwnSurnamePrefix()->getValue());
        $this->assertSame('Duck', $fn->getOwnSurname()->getValue());
        $this->assertSame('van', $fn->getSurnamePrefixFromPartnerSpouse()->getValue());
        $this->assertSame('Vandenberg', $fn->getSurnameFromPartnerSpouse()->getValue());
    }
}
