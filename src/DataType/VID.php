<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Version Identifier
 */
final class VID extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Id', ID::class, args: ['table' => 104]));
        $this->add(new TypeDefinition('Internationalization Code', CWE::class));
        $this->add(new TypeDefinition('International Version', CWE::class));
    }

    public function getId(): ID
    {
        return $this->getComponent(0);
    }

    public function getInternationalizationCode(): CWE
    {
        return $this->getComponent(1);
    }

    public function getInternationalVersion(): CWE
    {
        return $this->getComponent(2);
    }
}
