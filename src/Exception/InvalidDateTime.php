<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Exception;

final class InvalidDateTime extends \InvalidArgumentException implements HL7Exception
{
    public static function invalidValue(string $value): self
    {
        return new self("HL7 expected date/time in format YYYYMMDDHHMMSS.SSSS±OOOO, got: {$value}");
    }
}
