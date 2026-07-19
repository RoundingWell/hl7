<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use InvalidArgumentException;
use OutOfBoundsException;
use Override;
use ReflectionObject;

// @mago-expect lint:too-many-methods
// @mago-expect lint:cyclomatic-complexity
// @mago-expect lint:kan-defect
abstract class AbstractGroup implements Group
{
    use CanAssertNumbers;

    /** @var array<string, list<Structure>> */
    private array $structures = [];

    /** @var array<string, StructureDefinition> */
    private array $definitions = [];

    public function add(string $name, StructureDefinition $definition): void
    {
        if ($this->definitions[$name] ?? null) {
            throw new InvalidArgumentException(
                "Cannot add {$this->getName()}.{$name}, a structure with that key already exists",
            );
        }

        $this->definitions[$name] = $definition;
    }

    private function getDefinition(string $name): StructureDefinition
    {
        return (
            $this->definitions[$name] ?? throw new InvalidArgumentException(
                "Cannot get definition for {$this->getName()}.{$name}, it has not been added",
            )
        );
    }

    #[Override]
    public function getName(): string
    {
        return new ReflectionObject($this)->getShortName();
    }

    #[Override]
    public function getAll(string $name): array
    {
        return $this->structures[$name] ?? [];
    }

    #[Override]
    public function get(string $name): Structure
    {
        return $this->getRepetition($name, 0);
    }

    #[Override]
    public function getRepetition(string $name, int $repetition): Structure
    {
        $this->assertNaturalNumber($repetition);

        if ($this->structures[$name][$repetition] ?? null) {
            return $this->structures[$name][$repetition];
        }

        if (!$this->isRepeating($name) && $repetition > 0) {
            throw new OutOfBoundsException(
                "Cannot create repetition #{$repetition} of {$this->getName()}.{$name}, this structure is non-repeating",
            );
        }

        return $this->structures[$name][$repetition] = $this->getDefinition($name)->newInstance();
    }

    #[Override]
    public function getNames(): array
    {
        return array_keys($this->definitions);
    }

    #[Override]
    public function isRequired(string $name): bool
    {
        return $this->getDefinition($name)->isRequired();
    }

    #[Override]
    public function isRepeating(string $name): bool
    {
        return $this->getDefinition($name)->isRepeating();
    }

    /**
     * Parses segments into this group's structures, starting at $offset.
     *
     * Returns the offset of the first segment not consumed by this group, so callers
     * (including recursive calls into nested groups) can resume where this group stopped.
     *
     * @param list<SegmentElement> $segments
     * @param list<string>         $additionalNames segment names an enclosing scope may claim next
     */
    #[Override]
    public function parseSegments(Encoding $encoding, array $segments, array $additionalNames, int $offset): int
    {
        $names = $this->getNames();
        $pointer = 0;

        while (true) {
            $element = $segments[$offset] ?? null;

            if ($element === null) {
                // The stream is exhausted; this group consumed everything left.
                return $offset;
            }

            $match = $this->matchStructure($names, $pointer, $element->name);

            if ($match === null) {
                // Not part of this group's remaining structure.
                if (in_array($element->name, $additionalNames, true)) {
                    // An enclosing scope (or a new repetition of an ancestor) will claim it.
                    return $offset;
                }

                // Truly foreign: tolerate by skipping (decision 4A), keep parsing this group.
                $offset++;

                continue;
            }

            [$pointer, $name] = $match;

            $structure = $this->getRepetition($name, count($this->getAll($name)));

            $offset = $structure->parseSegments(
                $encoding,
                $segments,
                $this->additionalNames($pointer, $name, $additionalNames),
                $offset,
            );

            if (!$this->isRepeating($name)) {
                $pointer++;
            }
        }
    }

    /**
     * Serializes every structure in definition order, recursing into nested groups.
     *
     * The mirror of {@see parseSegments()}: segments serialize to their line, groups expand to
     * their contained lines, preserving the schema's structure order.
     */
    #[Override]
    public function serializeLines(Encoding $encoding): array
    {
        $lines = [];

        foreach ($this->getNames() as $name) {
            foreach ($this->getAll($name) as $structure) {
                $lines = [...$lines, ...$structure->serializeLines($encoding)];
            }
        }

        return $lines;
    }

    /**
     * Segment names that can legally follow the structure at $index within this group.
     *
     * @param list<string> $additionalNames
     *
     * @return list<string>
     */
    private function additionalNames(int $index, string $name, array $additionalNames): array
    {
        $names = [];

        // A repeating child can begin another repetition of itself.
        if ($this->isRepeating($name)) {
            $names = [...$names, ...$this->firstNamesOf($name)];
        }

        // Structures after the child within this group, up to and including the first required one.
        foreach (array_slice($this->getNames(), $index + 1) as $next) {
            $names = [...$names, ...$this->firstNamesOf($next)];

            if ($this->isRequired($next)) {
                break;
            }
        }

        // Plus whatever an enclosing scope could claim.
        return [...$names, ...$additionalNames];
    }

    /**
     * Finds the first structure at or after $from that the segment can legally begin.
     *
     * @param list<string> $names
     *
     * @return array{int, string}|null the matched index and structure name
     */
    private function matchStructure(array $names, int $from, string $segment): ?array
    {
        foreach (array_slice($names, $from, preserve_keys: true) as $index => $name) {
            if (in_array($segment, $this->firstNamesOf($name), true)) {
                return [$index, $name];
            }
        }

        return null;
    }

    /**
     * Segment names that can legally begin the given structure.
     *
     * @return list<string>
     */
    private function firstNamesOf(string $name): array
    {
        return $this->getDefinition($name)->newInstance()->firstNames();
    }

    /**
     * Segment names that can legally begin this group.
     *
     * @return list<string>
     */
    #[Override]
    public function firstNames(): array
    {
        $names = [];

        foreach ($this->getNames() as $name) {
            $names = [...$names, ...$this->firstNamesOf($name)];

            if ($this->isRequired($name)) {
                break;
            }
        }

        return $names;
    }
}
