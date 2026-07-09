<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Override;

class GenericPrimitive extends AbstractPrimitive
{
    #[Override]
    public function getName(): string
    {
        return 'UNKNOWN';
    }
}
