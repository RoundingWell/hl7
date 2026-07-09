<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Message\ADT;

use RoundingWell\HL7\AbstractGroup;
use RoundingWell\HL7\GenericSegment;
use RoundingWell\HL7\StructureDefinition;

/**
 * ADT_A06_INSURANCE group: IN1 + IN2 + IN3 + ROL
 */
final class A06Insurance extends AbstractGroup
{
    public function __construct()
    {
        $this->add('IN1', new StructureDefinition(GenericSegment::class, ['IN1'], isRequired: true));
        $this->add('IN2', new StructureDefinition(GenericSegment::class, ['IN2']));
        $this->add('IN3', new StructureDefinition(GenericSegment::class, ['IN3'], isRepeating: true));
        $this->add('ROL', new StructureDefinition(GenericSegment::class, ['ROL'], isRepeating: true));
    }
}
