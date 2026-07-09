<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Override;

class GenericGroup extends AbstractGroup
{
    use CanAssertNumbers;

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }
}
