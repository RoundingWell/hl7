<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Exception;

final class InvalidComponent extends \OutOfBoundsException implements HL7Exception
{
    public static function notDefined(string $type, int $number): self
    {
        return new self("Component '{$type}.{$number}' is not defined");
    }
}
