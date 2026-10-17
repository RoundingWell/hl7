<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use RoundingWell\HL7\AbstractMessage;
use RoundingWell\HL7\GenericSegment;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\StructureDefinition;

/**
 * A message with a counting structure between a required lead and a required tail, so the parse
 * walk probes the counting definition and then claims it repeatedly.
 */
final class FakeCountingMessage extends AbstractMessage
{
    public function __construct()
    {
        $this->add('MSH', new StructureDefinition(MSH::class, isRequired: true));
        $this->add('ZK1', new StructureDefinition(FakeCountingSegment::class, isRepeating: true));
        $this->add('ZZZ', new StructureDefinition(GenericSegment::class, ['ZZZ'], isRequired: true));
    }
}
