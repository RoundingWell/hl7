<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

/**
 * Represents HL7 values that contain components
 */
interface Composite extends Type
{
    /**
     * Return all components as a list
     *
     * @return list<Type>
     */
    public function getComponents(): array;

    /**
     * Return a specific component by number
     *
     * The index starts at zero, same as PHP arrays.
     */
    public function getComponent(int $number): Type;
}
