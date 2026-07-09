<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Message\ADT;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Message\ADT\A01Insurance;
use RoundingWell\HL7\Message\ADT\A01Procedure;
use RoundingWell\HL7\Message\ADT\A03Insurance;
use RoundingWell\HL7\Message\ADT\A03Procedure;
use RoundingWell\HL7\Message\ADT\A06Insurance;
use RoundingWell\HL7\Message\ADT\A06Procedure;

#[CoversClass(A01Procedure::class)]
#[CoversClass(A03Procedure::class)]
#[CoversClass(A06Procedure::class)]
#[CoversClass(A01Insurance::class)]
#[CoversClass(A03Insurance::class)]
#[CoversClass(A06Insurance::class)]
final class GroupStructureTest extends TestCase
{
    public function testProcedureLeadsWithPr1(): void
    {
        // A procedure group is entered on PR1; if its lead were wrong, parsing would misplace it.
        $this->assertSame(['PR1'], new A01Procedure()->firstNames());
    }

    public function testProcedureGroupsRegisterPr1ThenRol(): void
    {
        // Every ADT procedure group is PR1 (lead) then optional repeating ROL; order drives parsing.
        $this->assertSame(['PR1', 'ROL'], new A01Procedure()->getNames());
        $this->assertSame(['PR1', 'ROL'], new A03Procedure()->getNames());
        $this->assertSame(['PR1', 'ROL'], new A06Procedure()->getNames());
    }

    public function testInsuranceRegistersFullMemberSetForA01(): void
    {
        // A01 insurance must model IN1..RF1 so a fully-populated insurance block parses without loss.
        $this->assertSame(['IN1', 'IN2', 'IN3', 'ROL', 'AUT', 'RF1'], new A01Insurance()->getNames());
    }

    public function testA03InsuranceRegistersFullMemberSet(): void
    {
        // A03 insurance mirrors A01 (IN1..RF1); a dropped or reordered member would corrupt parsing.
        $this->assertSame(['IN1', 'IN2', 'IN3', 'ROL', 'AUT', 'RF1'], new A03Insurance()->getNames());
    }

    public function testInsuranceOmitsAutAndRf1ForA06(): void
    {
        // A06 insurance stops at ROL per HAPI; registering AUT/RF1 would misrepresent the structure.
        $this->assertSame(['IN1', 'IN2', 'IN3', 'ROL'], new A06Insurance()->getNames());
    }
}
