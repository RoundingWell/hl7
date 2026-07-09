<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Extended Person Name
 *
 * @mago-expect lint:too-many-methods
 */
final class XPN extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Family Name', FNx::class));
        $this->add(new TypeDefinition('Given Name', ST::class));
        $this->add(new TypeDefinition('Further Given Names', ST::class));
        $this->add(new TypeDefinition('Suffix', ST::class));
        $this->add(new TypeDefinition('Prefix', ST::class));
        $this->add(new TypeDefinition('Degree', ST::class));
        $this->add(new TypeDefinition('Name Type Code', ID::class, args: ['table' => 200]));
        $this->add(new TypeDefinition('Name Representation Code', ID::class, args: ['table' => 465]));
        $this->add(new TypeDefinition('Name Context', CWE::class));
        $this->add(new TypeDefinition('Name Validity Range', ST::class));
        $this->add(new TypeDefinition('Name Assembly Order', ID::class, args: ['table' => 444]));
        $this->add(new TypeDefinition('Effective Date', DTM::class));
        $this->add(new TypeDefinition('Expiration Date', DTM::class));
        $this->add(new TypeDefinition('Professional Suffix', ST::class));
        $this->add(new TypeDefinition('Called By', ST::class));
    }

    public function getFamilyName(): FNx
    {
        return $this->getComponent(0);
    }

    public function getGivenName(): ST
    {
        return $this->getComponent(1);
    }

    public function getFurtherGivenNames(): ST
    {
        return $this->getComponent(2);
    }

    public function getSuffix(): ST
    {
        return $this->getComponent(3);
    }

    public function getPrefix(): ST
    {
        return $this->getComponent(4);
    }

    public function getDegree(): ST
    {
        return $this->getComponent(5);
    }

    public function getNameTypeCode(): ID
    {
        return $this->getComponent(6);
    }

    public function getNameRepresentationCode(): ID
    {
        return $this->getComponent(7);
    }

    public function getNameContext(): CWE
    {
        return $this->getComponent(8);
    }

    public function getNameValidityRange(): ST
    {
        return $this->getComponent(9);
    }

    public function getNameAssemblyOrder(): ID
    {
        return $this->getComponent(10);
    }

    public function getEffectiveDate(): DTM
    {
        return $this->getComponent(11);
    }

    public function getExpirationDate(): DTM
    {
        return $this->getComponent(12);
    }

    public function getProfessionalSuffix(): ST
    {
        return $this->getComponent(13);
    }

    public function getCalledBy(): ST
    {
        return $this->getComponent(14);
    }
}
