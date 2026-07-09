<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Date/Time Range
 */
final class DR extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Start', TS::class));
        $this->add(new TypeDefinition('End', TS::class));
    }

    public function getStart(): TS
    {
        return $this->getComponent(0);
    }

    public function getEnd(): TS
    {
        return $this->getComponent(1);
    }
}
