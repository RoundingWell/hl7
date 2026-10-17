<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

/**
 * A value that can invalidate the segment line it was parsed from.
 */
interface TracksMutation
{
    public function track(DirtyFlag $dirty): void;
}
