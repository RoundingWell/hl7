<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Street Address
 */
final class SAD extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Street Address', ST::class));
        $this->add(new TypeDefinition('Street Name', ST::class));
        $this->add(new TypeDefinition('Dwelling Number', ST::class));
    }

    public function getStreetAddress(): ST
    {
        return $this->getComponent(0);
    }

    public function getStreetName(): ST
    {
        return $this->getComponent(1);
    }

    public function getDwellingNumber(): ST
    {
        return $this->getComponent(2);
    }
}
