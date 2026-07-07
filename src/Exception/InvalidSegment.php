<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Exception;

final class InvalidSegment extends \OutOfBoundsException implements HL7Exception
{
    public static function notDefined(string $message, string $segment): self
    {
        return new self("Segment '{$message}.{$segment}' is not defined");
    }
}
