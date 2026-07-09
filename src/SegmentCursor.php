<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use OutOfBoundsException;

final class SegmentCursor
{
    private int $position = 0;

    /** @var list<SegmentElement> */
    private readonly array $segments;

    /**
     * @param list<SegmentElement> $segments
     */
    public function __construct(SegmentElement ...$segments)
    {
        $this->segments = array_values($segments);
    }

    public function valid(): bool
    {
        // @mago-expect lint:no-isset
        return isset($this->segments[$this->position]);
    }

    public function peek(): SegmentElement
    {
        return $this->segments[$this->position] ?? throw new OutOfBoundsException('Segment cursor is exhausted');
    }

    public function next(): SegmentElement
    {
        try {
            return $this->peek();
        } finally {
            $this->position++;
        }
    }
}
