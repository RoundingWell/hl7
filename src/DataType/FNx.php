<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Family Name
 */
final class FNx extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Surname', ST::class));
        $this->add(new TypeDefinition('Own Surname Prefix', ST::class));
        $this->add(new TypeDefinition('Own Surname', ST::class));
        $this->add(new TypeDefinition('Surname Prefix From Partner Spouse', ST::class));
        $this->add(new TypeDefinition('Surname From Partner Spouse', ST::class));
    }

    public function getSurname(): ST
    {
        return $this->getComponent(0);
    }

    public function getOwnSurnamePrefix(): ST
    {
        return $this->getComponent(1);
    }

    public function getOwnSurname(): ST
    {
        return $this->getComponent(2);
    }

    public function getSurnamePrefixFromPartnerSpouse(): ST
    {
        return $this->getComponent(3);
    }

    public function getSurnameFromPartnerSpouse(): ST
    {
        return $this->getComponent(4);
    }
}
