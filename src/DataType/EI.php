<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Entity Identifier
 */
final class EI extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Id', ST::class));
        $this->add(new TypeDefinition('Namespace Id', IS::class, args: ['table' => 363]));
        $this->add(new TypeDefinition('Universal Id', ST::class));
        $this->add(new TypeDefinition('Universal Id Type', ID::class, args: ['table' => 301]));
    }

    public function getId(): ST
    {
        return $this->getComponent(0);
    }

    public function getNamespaceId(): IS
    {
        return $this->getComponent(1);
    }

    public function getUniversalId(): ST
    {
        return $this->getComponent(2);
    }

    public function getUniversalIdType(): ID
    {
        return $this->getComponent(3);
    }
}
