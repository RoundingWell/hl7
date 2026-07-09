<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Coded With Exceptions
 */
final class CWE extends AbstractComposite
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
}
