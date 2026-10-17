<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use function array_pop;
use function count;
use function end;
use function implode;

trait CanJoinElements
{
    /**
     * Joins serialized parts with a separator, dropping the trailing run of empty parts.
     *
     * Trailing empty subcomponents, components, repetitions, and fields carry no information in
     * HL7, so they are trimmed to produce canonical output. Interior empties are preserved because
     * a non-empty part follows them and their position is significant. The first $keep parts are
     * never trimmed, so a line can keep the field count it was received with.
     *
     * @param list<string> $parts
     */
    private function joinTrimmed(array $parts, string $separator, int $keep = 0): string
    {
        while (count($parts) > $keep && end($parts) === '') {
            array_pop($parts);
        }

        return implode($separator, $parts);
    }
}
