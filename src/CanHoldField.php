<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

trait CanHoldField
{
    private string $field = '<undefined>';

    public function setField(string $name): void
    {
        $this->field = $name;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
