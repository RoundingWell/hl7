<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

/**
 * Generates unique identifiers for message control IDs (MSH-10).
 */
interface IdGenerator
{
    public function generate(): string;
}
