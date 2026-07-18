<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

/**
 * An HL7 data type
 *
 * @see Composite
 * @see Primitive
 */
interface Type
{
    public function getName(): string;

    public function getExtraComponents(): ExtraComponents;

    public function clear(): void;

    /**
     * Consumes HL7 encoded data to set the value
     *
     * Components and subcomponents will be parsed from the data, rather than treated as plain text.
     */
    public function parse(Encoding $encoding, string $data): void;

    /**
     * Serializes the value back to HL7 encoded data
     *
     * The inverse of {@see parse()}: components and subcomponents are re-encoded with the
     * separators of the given encoding. Round-trips canonical input exactly.
     */
    public function serialize(Encoding $encoding): string;
}
