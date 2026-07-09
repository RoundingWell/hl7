<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Money
 */
final class MO extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Quantity', NM::class));
        $this->add(new TypeDefinition('Denomination', ID::class, args: ['table' => 913]));
    }

    public function getQuantity(): NM
    {
        return $this->getComponent(0);
    }

    public function getDenomination(): ID
    {
        return $this->getComponent(1);
    }
}
