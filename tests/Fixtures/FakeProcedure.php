<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use RoundingWell\HL7\AbstractGroup;
use RoundingWell\HL7\GenericSegment;
use RoundingWell\HL7\StructureDefinition;

final class FakeProcedure extends AbstractGroup
{
    public function __construct()
    {
        $this->add('PR1', new StructureDefinition(GenericSegment::class, ['PR1'], isRequired: true));
        $this->add('ROL', new StructureDefinition(GenericSegment::class, ['ROL'], isRepeating: true));
    }
}
