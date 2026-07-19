<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use Override;
use RoundingWell\HL7\Structure;

/**
 * A minimal {@see Structure} that is neither a Segment nor an AbstractGroup.
 *
 * Parsing can only dispatch segments and groups, so StructureDefinition must reject this
 * class; tests use it to exercise that rejection.
 */
final class FakeStructure implements Structure
{
    #[Override]
    public function getName(): string
    {
        return 'FAKE';
    }
}
