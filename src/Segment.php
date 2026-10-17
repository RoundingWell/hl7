<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

/**
 * An HL7 segment
 */
interface Segment extends Structure
{
    /**
     * Returns the list of fields at the given number
     *
     * This will return an array of {@see Type} instances, one for each field at the given number.
     * For a non-repeating field, this will return an array with a single element.
     * For a repeating field, this will return an array with one element per repetition.
     *
     * A repeating field may return an empty list.
     *
     * Reading a field parses it from the parsed line the first time, so a malformed value throws
     * here (for example InvalidValue, InvalidDateTime, or OutOfBoundsException for too many
     * repetitions), and again on every later read.
     *
     * @param positive-int $number starting at 1
     *
     * @return list<Type>
     */
    public function getField(int $number): array;

    /**
     * Return a specific repeitition of a field
     *
     * This will return a single {@see Type} instance at the given number and repetition.
     *
     * If the repetition does not exist, it will be created.
     *
     * Like {@see getField()}, this parses the field on first read and throws if it is malformed.
     *
     * @param positive-int $number starting at 1
     */
    public function getFieldRepetition(int $number, int $repetition): Type;

    /**
     * Returns the number of repitions for given field
     *
     * Like {@see getField()}, this parses the field on first read and throws if it is malformed.
     *
     * @param positive-int $number starting at 1
     */
    public function getLength(int $number): int;

    /**
     * Returns the names of all fields in this segment
     *
     * @return array<int, string>
     */
    public function getNames(): array;

    /**
     * Returns the number of fields in this segment
     */
    public function getFieldCount(): int;

    /**
     * Returns true if the field is required
     *
     * @param positive-int $number starting at 1
     */
    public function isRequired(int $number): bool;

    /**
     * Returns the maximum number of repetitions for a given field
     *
     * @param positive-int $number starting at 1
     */
    public function maxRepetitions(int $number): int;

    /**
     * Parse the given segment data, replacing any previous content
     *
     * Fields are parsed when they are first read, so a malformed field value does not throw here.
     */
    public function parse(Encoding $encoding, string $data): void;

    /**
     * Serializes this segment back to an HL7 encoded line
     *
     * With the delimiters the segment was parsed with, fields keep their received bytes unless
     * their content changed.
     */
    public function serialize(Encoding $encoding): string;
}
