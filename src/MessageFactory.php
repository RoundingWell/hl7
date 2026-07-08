<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use RoundingWell\HL7\Exception\InvalidFile;
use RoundingWell\HL7\Exception\InvalidMessage;
use RoundingWell\HL7\Segment\MSH;

final readonly class MessageFactory
{
    public function parseFile(string $path): Message
    {
        if (!is_file($path)) {
            throw InvalidFile::doesNotExist($path);
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw InvalidFile::cannotRead($path);
        }

        return $this->parse($content);
    }

    public function parse(string $data): Message
    {
        if (!str_starts_with($data, 'MSH')) {
            throw InvalidMessage::missingMSH();
        }

        // HL7 documents the line ending as CR, but this parser SHOULD support LF and CRLF as well.
        $eol = LineEnding::detect($data);

        // The delimiter MUST be the first character after the MSH segment ID.
        $delimiter = substr($data, 3, 1);

        if ($delimiter === '') {
            throw InvalidMessage::missingDelimiter();
        }

        // The encoding characters MUST be the next 4 characters after the delimiter.
        $enc = substr($data, 4, 4);

        if (strlen($enc) !== 4) {
            throw InvalidMessage::invalidEncoding();
        }

        // Once the encoding characters are detected, the encoding can be created.
        $encoding = new Encoding($eol->value, $delimiter, ...str_split($enc));

        // And then the segment factory can be created.
        $segmentFactory = new SegmentFactory($encoding);

        // And then the segments can be parsed.
        $segments = array_map($segmentFactory->parse(...), $this->splitSegments($encoding, $data));

        // @mago-expect analysis:less-specific-argument,possibly-null-argument,possibly-undefined-int-array-index
        return $this->create($this->getEventType($segments[0]), $segments);
    }

    /**
     * @return list<string>
     */
    private function splitSegments(Encoding $encoding, string $data): array
    {
        return explode($encoding->lineEnding, rtrim($data, $encoding->lineEnding));
    }

    private function getEventType(MSH $msh): string
    {
        return $msh->getMessageType()->triggerEvent->getValue();
    }

    /**
     * @param list<Segment> $segments
     */
    private function create(string $event, array $segments): Message
    {
        return match ($event) {
            'A01' => new Message\A01($segments),
            'A03' => new Message\A03($segments),
            'A06' => new Message\A06($segments),
            'A08' => new Message\A08($segments),
            default => new Message($segments),
        };
    }
}
