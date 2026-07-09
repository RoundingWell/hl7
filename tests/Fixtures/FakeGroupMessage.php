<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use RoundingWell\HL7\AbstractMessage;
use RoundingWell\HL7\GenericSegment;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\StructureDefinition;

final class FakeGroupMessage extends AbstractMessage
{
    public function __construct()
    {
        $this->add('MSH', new StructureDefinition(MSH::class, isRequired: true));
        $this->add('NK1', new StructureDefinition(GenericSegment::class, ['NK1'], isRepeating: true));
        $this->add('PV2', new StructureDefinition(GenericSegment::class, ['PV2']));
        $this->add('PROCEDURE', new StructureDefinition(FakeProcedure::class, isRepeating: true));
        $this->add('ZFA', new StructureDefinition(GenericSegment::class, ['ZFA'], isRequired: true));
    }
}
