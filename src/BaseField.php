<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use ReflectionClass;
use RoundingWell\HL7\DataType\Type;

/**
 * @template T of Type
 */
final class BaseField
{
    /** @var list<T> */
    private array $instances = [];

    public function __construct(
        private string $name,
        /** @var class-string<T> */
        private string $type,
        private bool $required = false,
        private bool $repeating = false,
        /** @var array<string, mixed> */
        private array $args = [],
    ) {
        assert(is_a($this->type, Type::class, true), "Expected {$this->type} to implement Type");
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isRepeating(): bool
    {
        return $this->repeating;
    }

    /**
     * @return T|list<T>
     */
    public function getInstance(): Type|array
    {
        if ($this->repeating) {
            return $this->instances;
        }

        if ($this->instances === []) {
            $this->instances = [
                // @mago-expect analysis:invalid-property-assignment-value
                new ReflectionClass($this->type)->newInstanceArgs($this->args),
            ];
        }

        // @mago-expect analysis:possibly-undefined-int-array-index,invalid-return-statement
        return $this->instances[0];
    }

    public function setRaw(Encoding $encoding, string $value): void
    {
        $instance = $this->getInstance();

        if (is_array($instance)) {
            $this->setRawRepetition($encoding, $value);

            return;
        }

        $instance->setRaw($encoding, $value);
    }

    private function setRawRepetition(Encoding $encoding, string $value): void
    {
        $this->instances = [];

        foreach ($this->splitRepetition($encoding, $value) as $val) {
            /** @var T $item */
            $item = new ReflectionClass($this->type)->newInstanceArgs($this->args);
            $item->setRaw($encoding, $val);

            $this->instances[] = $item;
        }
    }

    /**
     * @return list<string>
     */
    private function splitRepetition(Encoding $encoding, string $data): array
    {
        return explode($encoding->repetitionSeparator, $data);
    }
}
