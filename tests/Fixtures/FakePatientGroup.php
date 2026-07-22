<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use RoundingWell\HL7\AbstractGroup;
use RoundingWell\HL7\Segment\PID;
use RoundingWell\HL7\StructureDefinition;

/**
 * A minimal {@see AbstractGroup} wrapping a single typed {@see PID} segment, used to exercise
 * rendering of a group that contains schema-typed (rather than generic) content.
 */
final class FakePatientGroup extends AbstractGroup
{
    public function __construct()
    {
        $this->add('PID', new StructureDefinition(PID::class, isRequired: true));
    }
}
