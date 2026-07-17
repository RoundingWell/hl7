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

    /**
     * Decode a wire value back to its raw form
     *
     * Structural \CODE\ sequences (\F\ \S\ \T\ \R\ \E\) become the delimiters they
     * name. Any other \CODE\ sequence is a formatting/hex escape we do not interpret
     * and is preserved verbatim so the round-trip stays lossless, as is a dangling
     * escape character with no closing delimiter.
     */
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

    /**
     * Encode a value while being escape-aware
     *
     * This method is an exact inversion of decode(); it decodes \CODE\ sequences
     * that are not structural (i.e. not field/field separator/etc.) to preserve
     * them verbatim, so decode() sees them unchanged.
     */
    public function encode(string $value): string
    {
        // Accepted trade-off: because decode() already treats \...\ as opaque (see the
        // default branch of decodeEscape), a caller that passes literal text such as
        // "\.br\" gets it emitted verbatim, where an HL7 reader interprets it as a
        // formatting command. The stored value still round-trips in both directions;
        // only the wire-level "command vs literal" reading is ambiguous. This change
        // just extends decode()'s existing decision to encode().
        $esc = $this->escapeCharacter;
        $codes = $this->structuralCodes();
        $escapes = array_flip($codes);

        $result = '';
        $length = strlen($value);
        $index = 0;

        while ($index < $length) {
            $character = $value[$index];

            if ($character === $esc) {
                // A well-formed \CODE\ whose CODE is not structural is a preserved
                // formatting/hex escape; emit it verbatim so decode() sees it unchanged.
                $end = strpos($value, $esc, $index + 1);
                if ($end !== false && !array_key_exists(substr($value, $index + 1, $end - $index - 1), $codes)) {
                    $result .= substr($value, $index, $end - $index + 1);
                    $index = $end + 1;
                    continue;
                }
            }

            $code = $escapes[$character] ?? null;
            if ($code !== null) {
                $result .= $esc . $code . $esc;
                $index++;
                continue;
            }

            $result .= $character;
            $index++;
        }

        return $result;
    }

    private function decodeEscape(string $code, string $esc): string
    {
        // Unknown/formatting/hex escapes are preserved verbatim so round-trip is lossless.
        return $this->structuralCodes()[$code] ?? $esc . $code . $esc;
    }

    /**
     * The structural escape codes decode() consumes, mapped to the delimiters they
     * name. Shared by encode() and decodeEscape() so the two cannot drift on which
     * codes count as structural.
     *
     * @return array<string, string>
     */
    private function structuralCodes(): array
    {
        return [
            'F' => $this->fieldSeparator,
            'S' => $this->componentSeparator,
            'T' => $this->subcomponentSeparator,
            'R' => $this->repetitionSeparator,
            'E' => $this->escapeCharacter,
        ];
    }
}
