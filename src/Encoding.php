<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

final readonly class Encoding
{
    // @mago-expect lint:excessive-parameter-list
    public function __construct(
        public string $lineEnding = "\r",
        public string $fieldSeparator = '|',
        public string $componentSeparator = '^',
        public string $repetitionSeparator = '~',
        public string $escapeCharacter = '\\',
        public string $subcomponentSeparator = '&',
        // HL7 v2.7 added support for truncation characters, with '#' as the default.
        public string $truncationCharacter = '',
    ) {}

    public function encodingCharacters(): string
    {
        return (
            $this->componentSeparator
            . $this->repetitionSeparator
            . $this->escapeCharacter
            . $this->subcomponentSeparator
            . $this->truncationCharacter
        );
    }

    public function decode(string $value): string
    {
        $esc = $this->escapeCharacter;

        if (!str_contains($value, $esc)) {
            return $value;
        }

        $result = '';
        $length = strlen($value);
        $index = 0;

        while ($index < $length) {
            $character = $value[$index];
            if ($character !== $esc) {
                $result .= $character;
                $index++;
                continue;
            }

            $end = strpos($value, $esc, $index + 1);
            if ($end === false) {
                $result .= substr($value, $index);
                break;
            }

            $code = substr($value, $index + 1, $end - $index - 1);
            $result .= $this->decodeEscape($code, $esc);
            $index = $end + 1;
        }

        return $result;
    }

    public function encode(string $value): string
    {
        $esc = $this->escapeCharacter;
        $value = str_replace($esc, $esc . 'E' . $esc, $value);
        $value = str_replace($this->fieldSeparator, $esc . 'F' . $esc, $value);
        $value = str_replace($this->componentSeparator, $esc . 'S' . $esc, $value);
        $value = str_replace($this->repetitionSeparator, $esc . 'R' . $esc, $value);

        return str_replace($this->subcomponentSeparator, $esc . 'T' . $esc, $value);
    }

    private function decodeEscape(string $code, string $esc): string
    {
        return match ($code) {
            'F' => $this->fieldSeparator,
            'S' => $this->componentSeparator,
            'T' => $this->subcomponentSeparator,
            'R' => $this->repetitionSeparator,
            'E' => $esc,
            // Unknown/formatting/hex escapes are preserved verbatim so round-trip is lossless.
            default => $esc . $code . $esc,
        };
    }
}
