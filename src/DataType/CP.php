<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Composite Price
 */
final class CP extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Price', MO::class));
        $this->add(new TypeDefinition('Price Type', ID::class, args: ['table' => 205]));
        $this->add(new TypeDefinition('From Value', NM::class));
        $this->add(new TypeDefinition('To Value', NM::class));
        $this->add(new TypeDefinition('Range Units', CWE::class));
        $this->add(new TypeDefinition('Range Type', ID::class, args: ['table' => 298]));
    }

    public function getPrice(): MO
    {
        return $this->getComponent(0);
    }

    public function getPriceType(): ID
    {
        return $this->getComponent(1);
    }

    public function getFromValue(): NM
    {
        return $this->getComponent(2);
    }

    public function getToValue(): NM
    {
        return $this->getComponent(3);
    }

    public function getRangeUnits(): CWE
    {
        return $this->getComponent(4);
    }

    public function getRangeType(): ID
    {
        return $this->getComponent(5);
    }
}
