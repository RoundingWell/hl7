<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use Override;
use RoundingWell\HL7\Structure;

/**
 * A minimal, no-argument {@see Structure} used to exercise GenericGroup.
 *
 * GenericGroup instantiates registered structures with `new $model()`, and no production
 * Structure has a no-argument constructor, so tests supply this stand-in.
 */
final class FakeStructure implements Structure
{
    #[Override]
    public function getName(): string
    {
        return 'FAKE';
    }
}
