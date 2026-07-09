<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Timestamp
 */
final class TS extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Time', DTM::class));
        $this->add(new TypeDefinition('Precision', ID::class, args: ['table' => 529]));
    }

    public function getTime(): DTM
    {
        return $this->getComponent(0);
    }

    public function getPrecision(): ID
    {
        return $this->getComponent(1);
    }
}
