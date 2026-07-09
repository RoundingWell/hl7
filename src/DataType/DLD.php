<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Discharge to Location and Date
 */
final class DLD extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Discharged to Location', CWE::class));
        $this->add(new TypeDefinition('Effective Date', DTM::class));
    }

    public function getDischargeLocation(): CWE
    {
        return $this->getComponent(0);
    }

    public function getEffectiveDate(): DTM
    {
        return $this->getComponent(1);
    }
}
