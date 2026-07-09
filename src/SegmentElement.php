<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

final readonly class SegmentElement
{
    public function __construct(
        public string $name,
        public string $raw,
    ) {}
}
