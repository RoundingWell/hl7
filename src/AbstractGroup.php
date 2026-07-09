<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use InvalidArgumentException;
use OutOfBoundsException;
use Override;
use ReflectionObject;

// @mago-expect lint:too-many-methods
// @mago-expect lint:cyclomatic-complexity
abstract class AbstractGroup implements Group
{
    use CanAssertNumbers;

    /** @var array<string, list<Structure>> */
    private array $structures = [];

    /** @var array<string, StructureDefinition> */
    private array $definitions = [];

    public function add(string $name, StructureDefinition $definition): void
    {
        if (isset($this->definitions[$name])) { // @mago-expect lint:no-isset
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
        $names = $this->getNames();
        $pointer = 0;

        while ($cursor->valid()) {
            $segment = $cursor->peek()->name;
            $index = $this->matchStructure($names, $pointer, $segment);

            if ($index === null) {
                // Not part of this group's remaining structure.
                if (in_array($segment, $followNames, true)) {
                    // An enclosing scope (or a new repetition of an ancestor) will claim it.
                    return;
                }

                // Truly foreign: tolerate by skipping (decision 4A), keep parsing this group.
                $cursor->next();

                continue;
            }

            $pointer = $index;

            // @mago-expect analysis:possibly-undefined-int-array-index
            $name = $names[$index];

            assert(is_string($name), 'Matched index must reference a defined structure name');

            $structure = $this->getRepetition($name, count($this->getAll($name)));

            if ($structure instanceof self) {
                $structure->parseStructures($cursor, $encoding, $this->followNamesFor($index, $followNames));
            } else {
                assert($structure instanceof Segment, "Expected {$this->getName()}.{$name} to be a Segment");

                $structure->parse($encoding, $cursor->next()->raw);
            }

            if (!$this->isRepeating($name)) {
                $pointer++;
            }
        }
    }

    /**
     * Segment names that can legally follow the structure at $index within this group.
     *
     * @param list<string> $followNames
     *
     * @return list<string>
     */
    private function followNamesFor(int $index, array $followNames): array
    {
        $names = $this->getNames();
        $follow = [];

        // @mago-expect analysis:possibly-undefined-int-array-index
        $name = $names[$index];

        assert(is_string($name), 'Matched index must reference a defined structure name');

        // A repeating child can begin another repetition of itself.
        if ($this->isRepeating($name)) {
            $follow = [...$follow, ...$this->firstNamesOf($name)];
        }

        // Structures after the child within this group, up to and including the first required one.
        for ($i = $index + 1; $i < count($names); $i++) {
            // @mago-expect analysis:possibly-undefined-int-array-index
            $next = $names[$i];

            assert(is_string($next), 'Iterated index must reference a defined structure name');

            $follow = [...$follow, ...$this->firstNamesOf($next)];

            if ($this->isRequired($next)) {
                break;
            }
        }

        // Plus whatever an enclosing scope could claim.
        return [...$follow, ...$followNames];
    }

    /**
     * @param list<string> $names
     */
    private function matchStructure(array $names, int $from, string $segment): ?int
    {
        for ($index = $from; $index < count($names); $index++) {
            // @mago-expect analysis:possibly-undefined-int-array-index
            $name = $names[$index];

            assert(is_string($name), 'Matched index must reference a defined structure name');

            if (in_array($segment, $this->firstNamesOf($name), true)) {
                return $index;
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
        if (!$this->isGroup($name)) {
            return [$this->getDefinition($name)->newInstance()->getName()];
        }

        $probe = $this->getDefinition($name)->newInstance();

        assert($probe instanceof self, "Group {$this->getName()}.{$name} must extend AbstractGroup");

        return $probe->firstNames();
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
