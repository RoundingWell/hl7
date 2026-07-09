<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Processing Type
 */
final class PT extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Id', ID::class, args: ['table' => 103]));
        $this->add(new TypeDefinition('Mode', ID::class, args: ['table' => 207]));
    }

    public function getId(): ID
    {
        return $this->getComponent(0);
    }

    public function getMode(): ID
    {
        return $this->getComponent(1);
    }
}
