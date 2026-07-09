<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Job Code/Class
 */
final class JCC extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Job Code', CWE::class));
        $this->add(new TypeDefinition('Job Class', CWE::class));
        $this->add(new TypeDefinition('Job Description Text', TX::class));
    }

    public function getJobCode(): CWE
    {
        return $this->getComponent(0);
    }

    public function getJobClass(): CWE
    {
        return $this->getComponent(1);
    }

    public function getJobDescriptionText(): TX
    {
        return $this->getComponent(2);
    }
}
