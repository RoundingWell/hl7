<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Exception;

use OutOfBoundsException;

final class InvalidSegment extends OutOfBoundsException implements HL7Exception
{
    public static function notDefined(string $message, string $segment): self
    {
        return new self("Segment '{$message}.{$segment}' is not defined");
    }

    public static function invalidMSH(string $data): self
    {
        return new self("Invalid 'MSH' segment, required fields are missing: {$data}");
    }
}
