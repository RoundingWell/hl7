<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

/**
 * Part of an HL7 message
 *
 * @see Segment
 * @see Group
 */
interface Structure
{
    public function getName(): string;

    /**
     * Returns the segment names that can legally begin this structure
     *
     * For a segment this is its own name; for a group it is the names of its leading
     * structures, up to and including the first required one.
     *
     * @return list<string>
     */
    public function firstNames(): array;

    /**
     * Parses this structure from the segment stream, starting at $offset
     *
     * Returns the offset of the first segment not consumed, so the caller can resume
     * where this structure stopped.
     *
     * @param list<SegmentElement> $segments
     * @param list<string>         $additionalNames segment names an enclosing scope may claim next
     */
    public function parseSegments(Encoding $encoding, array $segments, array $additionalNames, int $offset): int;

    /**
     * Serializes this structure to HL7 encoded lines, one per segment
     *
     * @return list<string>
     */
    public function serializeLines(Encoding $encoding): array;
}
