<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use RoundingWell\HL7\Segment\EVN;
use RoundingWell\HL7\Segment\MSH;

final readonly class SegmentFactory
{
    public function __construct(
        private Encoding $encoding = new Encoding(),
    ) {}

    public function parse(string $data): Segment
    {
        $values = explode($this->encoding->fieldSeparator, $data);

        // The first value MUST be the segment identifier.
        $id = array_shift($values);

        if ($id === 'MSH') {
            // The field separator is consumed by explode(), but is required for MSH.1.
            // Add it back to the top of the values so it is correctly assigned.
            array_unshift($values, $this->encoding->fieldSeparator);
        }

        return $this->create($id, $values);
    }

    /**
     * @param list<string> $values
     */
    private function create(string $id, array $values): Segment
    {
        $segment = match ($id) {
            'MSH' => new MSH(),
            'EVN' => new EVN(),
            default => new Segment($id),
        };

        $segment->setRaw($this->encoding, $values);

        return $segment;
    }
}
