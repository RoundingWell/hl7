<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Closure;

/**
 * Write signal shared by one segment and every value parsed out of it.
 *
 * Marking runs the segment callback, which drops the bound raw line. Parsing marks as it fills
 * fields; the segment restores the line afterwards, so only a later mark — a real write — sticks.
 */
final readonly class DirtyFlag
{
    /**
     * @param Closure(): void $onDirty
     */
    public function __construct(
        private Closure $onDirty,
    ) {}

    public function mark(): void
    {
        ($this->onDirty)();
    }
}
