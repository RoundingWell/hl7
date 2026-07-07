<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Exception;

final class InvalidValue extends \InvalidArgumentException implements HL7Exception
{
    public static function minLength(string $type, int $min): self
    {
        return new self("Value of {$type} must be at least {$min} characters long");
    }

    public static function maxLength(string $type, int $max): self
    {
        return new self("Value of {$type} must be at most {$max} characters long");
    }

    public static function minValue(string $type, int $min): self
    {
        return new self("Value of {$type} must be greater than or equal to {$min}");
    }

    public static function maxValue(string $type, int $max): self
    {
        return new self("Value of {$type} must be less than or equal to {$max}");
    }
}
