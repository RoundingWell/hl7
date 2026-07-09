<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Exception;

final class InvalidPath extends \InvalidArgumentException implements HL7Exception
{
    public static function notNumeric(string $path): self
    {
        return new self("Dot path must only contain dots and numeric values, got: {$path}");
    }
}
