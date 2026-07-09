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
        // CX.4 (assigning authority) is an HD carried as subcomponents, so it must split
        // on '&' into its own components rather than landing whole on the namespace id.
        $cx = new CX();
        $cx->parse(new Encoding(), '10006579^A^^Facility&1.2.840&ISO^MRN');

        $this->assertSame('10006579', $cx->getId()->getValue());
        $this->assertSame('A', $cx->getIdentifierCheckDigit()->getValue());

        $authority = $cx->getAssigningAuthority();
        $this->assertSame('Facility', $authority->getNamespaceId()->getValue());
        $this->assertSame('1.2.840', $authority->getUniversalId()->getValue());
        $this->assertSame('ISO', $authority->getUniversalIdType()->getValue());

        $this->assertSame('MRN', $cx->getIdentifierTypeCode()->getValue());
    }

    public function testEveryComponentMapsToItsGetter(): void
    {
        // HD (authority/facility), TS (dates) and CWE (jurisdiction/agency) are all
        // composites carried as subcomponents, so they split on '&' into their parts.
        $cx = new CX();
        $cx->parse(
            new Encoding(),
            '10006579^7^M10^Auth&1.2.840&ISO^MRN^Fac&2.16.840&ISO^20240101^20251231^US&United States&ISO3166^HR&Human Resources&L^SEC1^M11',
        );

        $this->assertSame('10006579', $cx->getId()->getValue());
        $this->assertSame('7', $cx->getIdentifierCheckDigit()->getValue());
        $this->assertSame('M10', $cx->getCheckDigitScheme()->getValue());
        $this->assertSame('Auth', $cx->getAssigningAuthority()->getNamespaceId()->getValue());
        $this->assertSame('MRN', $cx->getIdentifierTypeCode()->getValue());
        $this->assertSame('Fac', $cx->getAssigningFacility()->getNamespaceId()->getValue());
        $this->assertSame('20240101', $cx->getEffectiveDate()->getTime()->getValue());
        $this->assertSame('20251231', $cx->getExpirationDate()->getTime()->getValue());
        $this->assertSame('US', $cx->getAssigningJurisdiction()->getIdentifier()->getValue());
        $this->assertSame('HR', $cx->getAssigningAgencyOrDepartment()->getIdentifier()->getValue());
        $this->assertSame('SEC1', $cx->getSecurityCheck()->getValue());
        $this->assertSame('M11', $cx->getSecurityCheckScheme()->getValue());
    }
}
