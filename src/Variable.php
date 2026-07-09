<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

interface Variable extends Type
{
    /**
     * Get this variable type
     *
     * Will return {@see GenericPrimitive} unless `setData()` has been called.
     */
    public function getData(): Type;

    /**
     * Modify this variable type
     *
     * The existing type value will be copied to the new type
     */
    public function setData(Type $data): void;
}
