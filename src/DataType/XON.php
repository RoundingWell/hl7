<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Extended Composite Name and Identification Number for Organizations
 *
 * @mago-expect lint:too-many-methods
 */
final class XON extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Organization Name', ST::class));
        $this->add(new TypeDefinition('Name Type Code', CWE::class));
        $this->add(new TypeDefinition('Id Number', ST::class));
        $this->add(new TypeDefinition('Identifier Check Digit', ST::class));
        $this->add(new TypeDefinition('Check Digit Scheme', ST::class));
        $this->add(new TypeDefinition('Assigning Authority', HD::class));
        $this->add(new TypeDefinition('Identifier Type Code', ID::class, args: ['table' => 203]));
        $this->add(new TypeDefinition('Assigning Facility', HD::class));
        $this->add(new TypeDefinition('Name Representation Code', ID::class, args: ['table' => 465]));
        $this->add(new TypeDefinition('Organization Identifier', ST::class));
    }

    public function getOrganizationName(): ST
    {
        return $this->getComponent(0);
    }

    public function getNameTypeCode(): CWE
    {
        return $this->getComponent(1);
    }

    public function getIdNumber(): ST
    {
        return $this->getComponent(2);
    }

    public function getIdentifierCheckDigit(): ST
    {
        return $this->getComponent(3);
    }

    public function getCheckDigitScheme(): ST
    {
        return $this->getComponent(4);
    }

    public function getAssigningAuthority(): HD
    {
        return $this->getComponent(5);
    }

    public function getIdentifierTypeCode(): ID
    {
        return $this->getComponent(6);
    }

    public function getAssigningFacility(): HD
    {
        return $this->getComponent(7);
    }

    public function getNameRepresentationCode(): ID
    {
        return $this->getComponent(8);
    }

    public function getOrganizationIdentifier(): ST
    {
        return $this->getComponent(9);
    }
}
