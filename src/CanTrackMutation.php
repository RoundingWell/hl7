<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

trait CanTrackMutation
{
    private ?DirtyFlag $dirty = null;

    private function markDirty(): void
    {
        $this->dirty?->mark();
    }
}
