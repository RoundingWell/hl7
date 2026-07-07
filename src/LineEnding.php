<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

enum LineEnding: string
{
    public static function detect(string $data): self
    {
        if (str_contains($data, "\r\n")) {
            return self::CRLF;
        }

        if (str_contains($data, "\n")) {
            return self::LF;
        }

        return self::CR;
    }

    case CR = "\r";
    case LF = "\n";
    case CRLF = "\r\n";
}
