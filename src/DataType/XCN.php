<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Extended Composite ID Number and Name for Persons
 *
 * @mago-expect lint:too-many-methods
 */
final class XCN extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Id', ST::class));
        $this->add(new TypeDefinition('Family Name', FNx::class));
        $this->add(new TypeDefinition('Given Name', ST::class));
        $this->add(new TypeDefinition('Further Given Names', ST::class));
        $this->add(new TypeDefinition('Suffix', ST::class));
        $this->add(new TypeDefinition('Prefix', ST::class));
        $this->add(new TypeDefinition('Degree', IS::class, args: ['table' => 360]));
        $this->add(new TypeDefinition('Source Table', IS::class, args: ['table' => 297]));
        $this->add(new TypeDefinition('Assigning Authority', HD::class));
        $this->add(new TypeDefinition('Name Type Code', ID::class, args: ['table' => 200]));
        $this->add(new TypeDefinition('Identifier Check Digit', ST::class));
        $this->add(new TypeDefinition('Check Digit Scheme', ID::class, args: ['table' => 61]));
        $this->add(new TypeDefinition('Identifier Type Code', ID::class, args: ['table' => 203]));
        $this->add(new TypeDefinition('Assigning Facility', HD::class));
        $this->add(new TypeDefinition('Name Representation Code', ID::class, args: ['table' => 465]));
        $this->add(new TypeDefinition('Name Context', CWE::class));
        $this->add(new TypeDefinition('Name Validity Range', DR::class));
        $this->add(new TypeDefinition('Name Assembly Order', ID::class, args: ['table' => 444]));
        $this->add(new TypeDefinition('Effective Date', TS::class));
        $this->add(new TypeDefinition('Expiration Date', TS::class));
        $this->add(new TypeDefinition('Professional Suffix', ST::class));
        $this->add(new TypeDefinition('Assigning Jurisdiction', CWE::class));
        $this->add(new TypeDefinition('Assigning Agency Or Department', CWE::class));
    }

    public function getId(): ST
    {
        return $this->getComponent(0);
    }

    public function getFamilyName(): FNx
    {
        return $this->getComponent(1);
    }

    public function getGivenName(): ST
    {
        return $this->getComponent(2);
    }

    public function getFurtherGivenNames(): ST
    {
        return $this->getComponent(3);
    }

    public function getSuffix(): ST
    {
        return $this->getComponent(4);
    }

    public function getPrefix(): ST
    {
        return $this->getComponent(5);
    }

    public function getDegree(): IS
    {
        return $this->getComponent(6);
    }

    public function getSourceTable(): IS
    {
        return $this->getComponent(7);
    }

    public function getAssigningAuthority(): HD
    {
        return $this->getComponent(8);
    }

    public function getNameTypeCode(): ID
    {
        return $this->getComponent(9);
    }

    public function getIdentifierCheckDigit(): ST
    {
        return $this->getComponent(10);
    }

    public function getCheckDigitScheme(): ID
    {
        return $this->getComponent(11);
    }

    public function getIdentifierTypeCode(): ID
    {
        return $this->getComponent(12);
    }

    public function getAssigningFacility(): HD
    {
        return $this->getComponent(13);
    }

    public function getNameRepresentationCode(): ID
    {
        return $this->getComponent(14);
    }

    public function getNameContext(): CWE
    {
        return $this->getComponent(15);
    }

    public function getNameValidityRange(): DR
    {
        return $this->getComponent(16);
    }

    public function getNameAssemblyOrder(): ID
    {
        return $this->getComponent(17);
    }

    public function getEffectiveDate(): TS
    {
        return $this->getComponent(18);
    }

    public function getExpirationDate(): TS
    {
        return $this->getComponent(19);
    }

    public function getProfessionalSuffix(): ST
    {
        return $this->getComponent(20);
    }

    public function getAssigningJurisdiction(): CWE
    {
        return $this->getComponent(21);
    }

    public function getAssigningAgencyOrDepartment(): CWE
    {
        return $this->getComponent(22);
    }
}
