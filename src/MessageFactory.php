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
        // Encoding MUST be detected before parsing segments.
        $encoding = $this->detectEncoding($data);

        // And then the segment factory can be created.
        $segmentFactory = new SegmentFactory($encoding);

        // And then the segments can be parsed.
        $segments = array_map($segmentFactory->parse(...), $this->splitSegments($encoding, $data));

        // @mago-expect analysis:less-specific-argument,possibly-null-argument,possibly-undefined-int-array-index
        return $this->create($segments[0], $segments);
    }

    private function detectEncoding(string $data): Encoding
    {
        if (!str_starts_with($data, 'MSH')) {
            throw InvalidMessage::missingMSH();
        }

        // Line ending SHOULD be CR, but MAY be CRLF or LF.
        $lineEnding = match (true) {
            str_contains($data, "\r\n") => "\r\n",
            str_contains($data, "\n") => "\n",
            default => "\r",
        };

        // Field separator MUST ALWAYS be the 4th character in a message, immediately after "MSH".
        $fieldSeparator = substr($data, 3, 1);

        if ($fieldSeparator === '') {
            throw InvalidMessage::missingFieldSeparator();
        }

        // Encoding characters MUST be the first field after the "MSH" identifier.
        $enc = $this->detectEncodingCharacters($fieldSeparator, $data);

        if (strlen($enc) < 4 || strlen($enc) > 5) {
            throw InvalidMessage::invalidEncoding();
        }

        return new Encoding($lineEnding, $fieldSeparator, ...str_split($enc));
    }

    private function detectEncodingCharacters(string $fieldSeparator, string $data): string
    {
        $offset = 4; // Skip MSH + delimiter
        $enc = '';

        do {
            // Read the next character from the string.
            $char = substr($data, $offset++, 1);

            // Stop reading when the next character is the field separator or the string ends.
            if ($char === $fieldSeparator || $char === '') {
                break;
            }

            $enc .= $char;
        } while (true);

        return $enc;
    }

    /**
     * @return list<string>
     */
    private function splitSegments(Encoding $encoding, string $data): array
    {
        return explode($encoding->lineEnding, rtrim($data, $encoding->lineEnding));
    }

    /**
     * @param list<Segment> $segments
     */
    private function create(MSH $msh, array $segments): Message
    {
        $type = $msh->getMessageType()->messageType->getValue();
        $event = $msh->getMessageType()->triggerEvent->getValue();

        if ($type === 'ADT') {
            return match ($event) {
                'A01' => new Message\ADT\A01($segments),
                'A03' => new Message\ADT\A03($segments),
                'A06' => new Message\ADT\A06($segments),
                'A08' => new Message\ADT\A08($segments),
                default => new Message($segments),
            };
        }

        return new Message($segments);
    }
}
