<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Extended Composite ID with Check Digit
 *
 * @mago-expect lint:too-many-methods
 */
final class CX extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Id', ST::class));
        $this->add(new TypeDefinition('Identifier Check Digit', ST::class));
        $this->add(new TypeDefinition('Check Digit Scheme', ID::class, args: ['table' => 61]));
        $this->add(new TypeDefinition('Assigning Authority', HD::class));
        $this->add(new TypeDefinition('Identifier Type Code', ID::class, args: ['table' => 203]));
        $this->add(new TypeDefinition('Assigning Facility', HD::class));
        $this->add(new TypeDefinition('Effective Date', TS::class));
        $this->add(new TypeDefinition('Expiration Date', TS::class));
        $this->add(new TypeDefinition('Assigning Jurisdiction', CWE::class));
        $this->add(new TypeDefinition('Assigning Agency Or Department', CWE::class));
        $this->add(new TypeDefinition('Security Check', ST::class));
        $this->add(new TypeDefinition('Security Check Scheme', ID::class, args: ['table' => 904]));
    }

    public function getId(): ST
    {
        return $this->getComponent(0);
    }

    public function getIdentifierCheckDigit(): ST
    {
        return $this->getComponent(1);
    }

    public function getCheckDigitScheme(): ID
    {
        return $this->getComponent(2);
    }

    public function getAssigningAuthority(): HD
    {
        return $this->getComponent(3);
    }

    public function getIdentifierTypeCode(): ID
    {
        return $this->getComponent(4);
    }

    public function getAssigningFacility(): HD
    {
        return $this->getComponent(5);
    }

    public function getEffectiveDate(): TS
    {
        return $this->getComponent(6);
    }

    public function getExpirationDate(): TS
    {
        return $this->getComponent(7);
    }

    public function getAssigningJurisdiction(): CWE
    {
        return $this->getComponent(8);
    }

    public function getAssigningAgencyOrDepartment(): CWE
    {
        return $this->getComponent(9);
    }

    public function getSecurityCheck(): ST
    {
        return $this->getComponent(10);
    }

    public function getSecurityCheckScheme(): ID
    {
        return $this->getComponent(11);
    }
}
