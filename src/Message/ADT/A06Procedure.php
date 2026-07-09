<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Message\ADT;

use RoundingWell\HL7\AbstractGroup;
use RoundingWell\HL7\GenericSegment;
use RoundingWell\HL7\StructureDefinition;

/**
 * ADT_A06_PROCEDURE group: PR1 (Procedures) + ROL (Role, optional repeating)
 */
final class A06Procedure extends AbstractGroup
{
    public function __construct()
    {
        $this->add('PR1', new StructureDefinition(GenericSegment::class, ['PR1'], isRequired: true));
        $this->add('ROL', new StructureDefinition(GenericSegment::class, ['ROL'], isRepeating: true));
    }
}
