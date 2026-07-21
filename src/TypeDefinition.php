<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use InvalidArgumentException;
use ReflectionClass;
use RuntimeException;

final readonly class TypeDefinition
{
    use CanAssertNumbers;

    public function __construct(
        private string $name = '',
        // A field sits above the component level, so an undefined field defaults to a
        // GenericComposite -- preserving its component structure ("^" depth) rather than
        // flattening it into a primitive. Trade-off: unlike Varies, a GenericComposite is not a
        // deferred placeholder, so a generic field cannot be upgraded to a concrete type via
        // setData() after parsing. Subcomponents still resolve to Varies via ExtraComponents.
        /** @var class-string<Type> */
        private string $type = GenericComposite::class,
        /** @var array<string, mixed> */
        private array $args = [],
        private bool $isRequired = false,
        private int $maxReps = 0,
    ) {
        if (!is_subclass_of($this->type, Type::class, true)) {
            throw new InvalidArgumentException("Cannot define {$this->type}, it does not implement Type");
        }

        $this->assertNaturalNumber($this->maxReps);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function getMaxReps(): int
    {
        return $this->maxReps;
    }

    public function newInstance(): Type
    {
        $instance = new ReflectionClass($this->type)->newInstanceArgs($this->args) ?? throw new RuntimeException(
            "Unable to create instance of {$this->type}",
        );

        // Pass the field name into the instance for debugging.
        $instance->setField($this->name);

        return $instance;
    }
}
