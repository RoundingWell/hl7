<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use Override;
use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\Encoding;

/**
 * Counts field parses so a test can tell a schema query from a real read.
 */
final class FakeHydratingSegment extends AbstractSegment
{
    public int $hydrations = 0;

    #[Override]
    protected function populate(Encoding $encoding, string $data): void
    {
        $this->hydrations++;

        parent::populate($encoding, $data);
    }
}
