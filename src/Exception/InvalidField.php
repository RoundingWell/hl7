<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Exception;

final class InvalidField extends \OutOfBoundsException implements HL7Exception
{
    public static function notDefined(string $segment, int $number): self
    {
        return new self("Field '{$segment}.{$number}' is not defined");
    }

    public static function tooLow(string $segment, int $number): self
    {
        return new self("Field '{$segment}.{$number}' is too low; minimum number is 1");
    }
}
