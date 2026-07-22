<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use RoundingWell\HL7\AbstractMessage;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\StructureDefinition;

/**
 * A message whose only optional structure is a {@see FakePatientGroup}, used to exercise rendering
 * of a nested group that holds a typed segment.
 */
final class FakeTypedGroupMessage extends AbstractMessage
{
    public function __construct()
    {
        $this->add('MSH', new StructureDefinition(MSH::class, isRequired: true));
        $this->add('PATIENT', new StructureDefinition(FakePatientGroup::class));
    }
}
