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

    /** @var array<string, list<Segment|AbstractGroup>> */
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

    /**
     * @return list<Segment|AbstractGroup>
     */
    #[Override]
    public function getAll(string $name): array
    {
        return $this->structures[$name] ?? [];
    }

    #[Override]
    public function get(string $name): Segment|AbstractGroup
    {
        return $this->getRepetition($name, 0);
    }

    #[Override]
    public function getRepetition(string $name, int $repetition): Segment|AbstractGroup
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

    #[Override]
    public function isGroup(string $name): bool
    {
        return $this->getDefinition($name)->isGroup();
    }

    /**
     * @param list<string> $followNames segment names an enclosing scope may claim next
     */
    protected function parseStructures(SegmentCursor $cursor, Encoding $encoding, array $followNames = []): void
    {
        // Structures that may still match, in definition order. HL7 structures are positional:
        // a match discards the candidates before it, and a non-repeating match is itself spent.
        $candidates = $this->getNames();

        while ($cursor->valid()) {
            $segment = $cursor->peek()->name;
            $match = $this->matchStructure($candidates, $segment);

            if ($match === null) {
                // Not part of this group's remaining structure.
                if (in_array($segment, $followNames, true)) {
                    // An enclosing scope (or a new repetition of an ancestor) will claim it.
                    return;
                }

                // Truly foreign: tolerate by skipping (decision 4A), keep parsing this group.
                $cursor->next();

                continue;
            }

            [$name, $after] = $match;

            $structure = $this->getRepetition($name, count($this->getAll($name)));

            if ($structure instanceof self) {
                $structure->parseStructures($cursor, $encoding, $this->followNamesFor($name, $after, $followNames));
            } else {
                $structure->parse($encoding, $cursor->next()->raw);
            }

            $candidates = $this->isRepeating($name) ? [$name, ...$after] : $after;
        }
    }

    /**
     * Serializes every structure in definition order, recursing into nested groups.
     *
     * The mirror of {@see parseStructures()}: segments are serialized to lines, groups expand to
     * their contained lines, preserving the schema's structure order.
     *
     * @return list<string>
     */
    protected function serializeStructures(Encoding $encoding): array
    {
        $lines = [];

        foreach ($this->getNames() as $name) {
            foreach ($this->getAll($name) as $structure) {
                if ($structure instanceof self) {
                    $lines = [...$lines, ...$structure->serializeStructures($encoding)];

                    continue;
                }

                $lines[] = $structure->serialize($encoding);
            }
        }

        return $lines;
    }

    /**
     * Segment names that can legally follow the given structure within this group.
     *
     * @param list<string> $after structure names declared after it, in order
     * @param list<string> $followNames
     *
     * @return list<string>
     */
    private function followNamesFor(string $name, array $after, array $followNames): array
    {
        // A repeating child can begin another repetition of itself.
        $follow = $this->isRepeating($name) ? $this->firstNamesOf($name) : [];

        // Structures after the child within this group, up to and including the first required one.
        foreach ($after as $next) {
            $follow = [...$follow, ...$this->firstNamesOf($next)];

            if ($this->isRequired($next)) {
                break;
            }
        }

        // Plus whatever an enclosing scope could claim.
        return [...$follow, ...$followNames];
    }

    /**
     * Finds the first candidate structure the given segment can begin.
     *
     * @param list<string> $candidates
     *
     * @return array{string, list<string>}|null the matched name and the candidates after it
     */
    private function matchStructure(array $candidates, string $segment): ?array
    {
        foreach ($candidates as $index => $name) {
            if (in_array($segment, $this->firstNamesOf($name), true)) {
                return [$name, array_slice($candidates, $index + 1)];
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
        $structure = $this->getDefinition($name)->newInstance();

        return $structure instanceof self ? $structure->firstNames() : [$structure->getName()];
    }

    /**
     * Segment names that can legally begin this group.
     *
     * @return list<string>
     */
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
