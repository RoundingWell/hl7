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

        [$mshLine] = explode($encoding->lineEnding, $data, 2);

        $msh = new MSH();
        $msh->parse($encoding, $mshLine);

        $message = $this->create($msh);
        $message->parse($encoding, $data);

        return $message;
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

    private function create(MSH $msh): Message
    {
        $type = $msh->getMessageType()->getMessageType()->getValue();
        $event = $msh->getMessageType()->getTriggerEvent()->getValue();
        $version = $msh->getVersionId()->getId()->getValue();

        if ($type === 'ADT') {
            return match ($event) {
                'A01' => new Message\ADT\A01(),
                'A03' => new Message\ADT\A03(),
                'A04' => new Message\ADT\A04(),
                'A06' => new Message\ADT\A06(),
                'A07' => new Message\ADT\A07(),
                'A08' => new Message\ADT\A08(),
                'A13' => new Message\ADT\A13(),
                default => new GenericMessage("{$type}_{$event}", $version),
            };
        }

        return new GenericMessage("{$type}_{$event}", $version);
    }
}
