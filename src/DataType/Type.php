<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\Encoding;

interface Type
{
    public function setRaw(Encoding $encoding, string $value, int $depth = 0): void;
}
