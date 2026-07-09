<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Financial Class
 */
final class FC extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Financial Class Code', CWE::class));
        $this->add(new TypeDefinition('Effective Date', DTM::class));
    }

    public function getFinancialClassCode(): CWE
    {
        return $this->getComponent(0);
    }

    public function getEffectiveDate(): DTM
    {
        return $this->getComponent(1);
    }
}
