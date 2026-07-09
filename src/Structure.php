<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

/**
 * Part of an HL7 message
 *
 * @see Segment
 * @see Group
 */
interface Structure
{
    public function getName(): string;
}
