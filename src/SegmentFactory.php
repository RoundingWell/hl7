<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

final readonly class SegmentFactory
{
    public function __construct(
        private Encoding $encoding = new Encoding(),
    ) {}

    public function parse(string $data): BaseSegment
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
    private function create(string $id, array $values): BaseSegment
    {
        $segment = match ($id) {
            'MSH' => new Segment\MSH(),
            'EVN' => new Segment\EVN(),
            'PID' => new Segment\PID(),
            'NK1' => new Segment\NK1(),
            'PV1' => new Segment\PV1(),
            'PV2' => new Segment\PV2(),
            'DG1' => new Segment\DG1(),
            'DRG' => new Segment\DRG(),
            'OBX' => new Segment\OBX(),
            default => new BaseSegment($id),
        };

        $segment->setRaw($this->encoding, $values);

        return $segment;
    }
}
