<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Coded No Exceptions
 *
 * @mago-expect lint:too-many-methods
 */
final class CNE extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Identifier', ST::class));
        $this->add(new TypeDefinition('Text', ST::class));
        $this->add(new TypeDefinition('Coding System', ID::class, args: ['table' => 396]));
        $this->add(new TypeDefinition('Alternate Identifier', ST::class));
        $this->add(new TypeDefinition('Alternate Text', ST::class));
        $this->add(new TypeDefinition('Alternate Coding System', ID::class, args: ['table' => 396]));
        $this->add(new TypeDefinition('Coding System Version', ST::class));
        $this->add(new TypeDefinition('Alternate Coding System Version', ST::class));
        $this->add(new TypeDefinition('Original Text', ST::class));
        $this->add(new TypeDefinition('Second Alternate Identifier', ST::class));
        $this->add(new TypeDefinition('Second Alternate Text', ST::class));
        $this->add(new TypeDefinition('Second Alternate Coding System', ID::class, args: ['table' => 396]));
        $this->add(new TypeDefinition('Second Alternate Coding System Version', ST::class));
        $this->add(new TypeDefinition('Coding System Oid', ST::class));
        $this->add(new TypeDefinition('Value Set Oid', ST::class));
        $this->add(new TypeDefinition('Value Set Version', DTM::class));
        $this->add(new TypeDefinition('Alternate Coding System Oid', ST::class));
        $this->add(new TypeDefinition('Alternate Value Set Oid', ST::class));
        $this->add(new TypeDefinition('Alternate Value Set Version', DTM::class));
        $this->add(new TypeDefinition('Second Alternate Coding System Oid', ST::class));
        $this->add(new TypeDefinition('Second Alternate Value Set Oid', ST::class));
        $this->add(new TypeDefinition('Second Alternate Value Set Version', DTM::class));
    }

    public function getIdentifier(): ST
    {
        return $this->getComponent(0);
    }

    public function getText(): ST
    {
        return $this->getComponent(1);
    }

    public function getCodingSystem(): ID
    {
        return $this->getComponent(2);
    }

    public function getAlternateIdentifier(): ST
    {
        return $this->getComponent(3);
    }

    public function getAlternateText(): ST
    {
        return $this->getComponent(4);
    }

    public function getAlternateCodingSystem(): ID
    {
        return $this->getComponent(5);
    }

    public function getCodingSystemVersion(): ST
    {
        return $this->getComponent(6);
    }

    public function getAlternateCodingSystemVersion(): ST
    {
        return $this->getComponent(7);
    }

    public function getOriginalText(): ST
    {
        return $this->getComponent(8);
    }

    public function getSecondAlternateIdentifier(): ST
    {
        return $this->getComponent(9);
    }

    public function getSecondAlternateText(): ST
    {
        return $this->getComponent(10);
    }

    public function getSecondAlternateCodingSystem(): ID
    {
        return $this->getComponent(11);
    }

    public function getSecondAlternateCodingSystemVersion(): ST
    {
        return $this->getComponent(12);
    }

    public function getCodingSystemOid(): ST
    {
        return $this->getComponent(13);
    }

    public function getValueSetOid(): ST
    {
        return $this->getComponent(14);
    }

    public function getValueSetVersion(): DTM
    {
        return $this->getComponent(15);
    }

    public function getAlternateCodingSystemOid(): ST
    {
        return $this->getComponent(16);
    }

    public function getAlternateValueSetOid(): ST
    {
        return $this->getComponent(17);
    }

    public function getAlternateValueSetVersion(): DTM
    {
        return $this->getComponent(18);
    }

    public function getSecondAlternateCodingSystemOid(): ST
    {
        return $this->getComponent(19);
    }

    public function getSecondAlternateValueSetOid(): ST
    {
        return $this->getComponent(20);
    }

    public function getSecondAlternateValueSetVersion(): DTM
    {
        return $this->getComponent(21);
    }
}
