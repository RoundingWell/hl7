<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use InvalidArgumentException;
use ReflectionClass;
use RuntimeException;

final readonly class StructureDefinition
{
    use CanAssertNumbers;

    public function __construct(
        /** @var class-string<Structure> */
        private string $type,
        /** @var array<array-key, mixed> */
        private array $args = [],
        private bool $isRequired = false,
        private bool $isRepeating = false,
    ) {
        // Parsing dispatches on exactly two cases (a Segment consumes a line, an AbstractGroup
        // recurses), so anything else is rejected here rather than mid-parse.
        if (
            !is_subclass_of($this->type, Segment::class, true)
            && !is_subclass_of($this->type, AbstractGroup::class, true)
        ) {
            throw new InvalidArgumentException(
                "Cannot define {$this->type}, it must be a Segment or extend AbstractGroup",
            );
        }
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function isRepeating(): bool
    {
        return $this->isRepeating;
    }

    public function isGroup(): bool
    {
        return is_subclass_of($this->type, Group::class, true);
    }

    public function newInstance(): Segment|AbstractGroup
    {
        return (
            new ReflectionClass($this->type)->newInstanceArgs($this->args) ?? throw new RuntimeException(
                "Unable to create instance of {$this->type}",
            )
        );
    }
}
