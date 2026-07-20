<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

interface Group extends Structure
{
    /**
     * Returns all structures with the given name
     *
     * For example, if the group contains an "MSH" structure, this method would return
     * a one-element list containing the "MSH" structure.
     *
     * Multiple structures would be returned if the structure is repeating.
     *
     * If the structure is not found, an empty list is returned.
     *
     * @return list<Structure>
     */
    public function getAll(string $name): array;

    /**
     * Returns the first structure with the given name
     *
     * If the structure does not exist, it will be created.
     */
    public function get(string $name): Structure;

    /**
     * Returns the specific repetition of a structure
     *
     * If the structure does not already exist, it will be created.
     *
     * @param int $repetition zero or greater
     */
    public function getRepetition(string $name, int $repetition): Structure;

    /**
     * Returns the names of all structures in the group
     *
     * @return list<string>
     */
    public function getNames(): array;

    /**
     * Returns whether or not the given structure is required
     */
    public function isRequired(string $name): bool;

    /**
     * Returns whether or not the given structure is repeating
     */
    public function isRepeating(string $name): bool;
}
