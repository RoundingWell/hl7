<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Exception;

final class InvalidFile extends \RuntimeException implements HL7Exception
{
    public static function doesNotExist(string $path): self
    {
        return new self("HL7 file does not exist: {$path}");
    }

    public static function cannotRead(string $path): self
    {
        return new self("HL7 file cannot be read: {$path}");
    }
}
