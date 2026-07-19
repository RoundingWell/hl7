<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use RoundingWell\HL7\Segment\MSH;

/**
 * An HL7 message
 */
interface Message extends Group
{
    public function getVersion(): string;

    public function getMSH(): MSH;

    public function parse(Encoding $encoding, string $data): void;

    public function serialize(Encoding $encoding): string;
}
