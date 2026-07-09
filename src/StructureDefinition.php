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
        if (!is_subclass_of($this->type, Structure::class, true)) {
            throw new InvalidArgumentException("Cannot define {$this->type}, it does not implement Structure");
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

    public function newInstance(): Structure
    {
        return (
            new ReflectionClass($this->type)->newInstanceArgs($this->args) ?? throw new RuntimeException(
                "Unable to create instance of {$this->type}",
            )
        );
    }
}
