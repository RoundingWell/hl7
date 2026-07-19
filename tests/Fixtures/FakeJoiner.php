<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use RoundingWell\HL7\CanJoinElements;

/**
 * Exposes the private {@see CanJoinElements::joinTrimmed()} so the trailing-trim rule can be
 * tested directly, independent of any serialize() caller.
 */
final class FakeJoiner
{
    use CanJoinElements;

    /**
     * @param list<string> $parts
     */
    public function join(array $parts, string $separator): string
    {
        return $this->joinTrimmed($parts, $separator);
    }
}
