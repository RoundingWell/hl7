<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Override;
use RoundingWell\HL7\Segment\MSH;

abstract class AbstractMessage extends AbstractGroup implements Message
{
    #[Override]
    public function getVersion(): string
    {
        return $this->getMSH()->getVersionId()->getId()->getValue();
    }

    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        $segments = [];

        foreach (array_filter(explode($encoding->lineEnding, $data)) as $line) {
            [$name] = explode($encoding->fieldSeparator, $line, 2);

            $segments[] = new SegmentElement($name, $line);
        }

        $this->parseStructures(new SegmentCursor(...$segments), $encoding);
    }

    public function getSegment(string $name, int $repetition): Segment
    {
        return $this->getRepetition($name, $repetition);
    }

    #[Override]
    public function getMSH(): MSH
    {
        return $this->getSegment('MSH', 0);
    }
}
