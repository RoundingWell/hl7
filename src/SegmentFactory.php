<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

final readonly class SegmentFactory
{
    public function __construct(
        private Encoding $encoding = new Encoding(),
    ) {}

    public function parse(string $data): Segment
    {
        // The first value MUST be the segment identifier.
        [$id] = explode($this->encoding->fieldSeparator, $data, 2);

        $segment = $this->create($id);

        // The segment parses its own name back off the data, so pass the full line.
        $segment->parse($this->encoding, $data);

        return $segment;
    }

    private function create(string $id): Segment
    {
        return match ($id) {
            'MSH' => new Segment\MSH(),
            'EVN' => new Segment\EVN(),
            'PID' => new Segment\PID(),
            'NK1' => new Segment\NK1(),
            'PV1' => new Segment\PV1(),
            'PV2' => new Segment\PV2(),
            'DG1' => new Segment\DG1(),
            'DRG' => new Segment\DRG(),
            'OBX' => new Segment\OBX(),
            default => new GenericSegment($id),
        };
    }
}
