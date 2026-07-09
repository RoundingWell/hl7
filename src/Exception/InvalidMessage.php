<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Exception;

final class InvalidMessage extends \InvalidArgumentException implements HL7Exception
{
    public static function missingMSH(): self
    {
        return new self("HL7 message must start with 'MSH' segment");
    }

    public static function missingFieldSeparator(): self
    {
        return new self("HL7 message must have a field separator in the 'MSH' segment");
    }

    public static function invalidEncoding(): self
    {
        return new self("HL7 message must have 4+ encoding characters in the 'MSH' segment");
    }
}
