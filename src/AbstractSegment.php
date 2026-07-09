<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use InvalidArgumentException;
use OutOfBoundsException;
use Override;
use ReflectionObject;

// @mago-expect lint:too-many-methods
abstract class AbstractSegment implements Segment
{
    use CanAssertNumbers;

    /** @var array<int, list<Type>> */
    private array $fields = [];

    /** @var list<TypeDefinition> */
    private array $definitions = [];

    public function add(TypeDefinition $definition): void
    {
        $this->definitions[] = $definition;
    }

    private function getDefinition(int $number): TypeDefinition
    {
        return (
            $this->definitions[$number - 1] ?? throw new InvalidArgumentException(
                "Cannot get definition for {$this->getName()}.{$number}, it has not been added",
            )
        );
    }

    #[Override]
    public function getName(): string
    {
        return new ReflectionObject($this)->getShortName();
    }

    #[Override]
    public function getField(int $number): array
    {
        $this->assertPositiveNumber($number);

        return $this->fields[$number - 1] ?? [];
    }

    // @mago-expect lint:halstead
    #[Override]
    public function getFieldRepetition(int $number, int $repetition): Type
    {
        $this->assertPositiveNumber($number);
        $this->assertNaturalNumber($repetition);

        // Field number is 1-based, but array indices are 0-based.
        $idx = $number - 1;

        if ($this->fields[$idx][$repetition] ?? null) {
            return $this->fields[$idx][$repetition];
        }

        // Add any fields that have not been defined, up to the requested number.
        for ($m = max($number - $this->getFieldCount(), 0); $m > 0; $m--) {
            $this->add(new TypeDefinition());
        }

        $total = count($this->fields[$idx] ?? []);

        if ($repetition > $total) {
            throw new OutOfBoundsException(
                "Cannot get repetition #{$repetition} of {$this->getName()}.{$number}, there are only {$total} repetitions",
            );
        }

        $max = $this->maxRepetitions($number);

        if ($max !== 0 && $repetition > $max) {
            throw new OutOfBoundsException(
                "Cannot get repetition #{$repetition} of {$this->getName()}.{$number}, only {$max} repetitions are allowed",
            );
        }

        $definition = $this->definitions[$number - 1] ?? throw new InvalidArgumentException(
            "Cannot create field {$this->getName()}.{$number}, it has not been added",
        );

        return $this->fields[$idx][$repetition] = $definition->newInstance();
    }

    #[Override]
    public function getLength(int $number): int
    {
        $this->assertPositiveNumber($number);

        return count($this->fields[$number - 1] ?? []);
    }

    #[Override]
    public function getNames(): array
    {
        return array_map(static fn(TypeDefinition $definition): string => $definition->getName(), $this->definitions);
    }

    #[Override]
    public function getFieldCount(): int
    {
        return count($this->definitions);
    }

    #[Override]
    public function isRequired(int $number): bool
    {
        $this->assertPositiveNumber($number);

        return $this->getDefinition($number)->isRequired();
    }

    #[Override]
    public function maxRepetitions(int $number): int
    {
        $this->assertPositiveNumber($number);

        return $this->getDefinition($number)->getMaxReps();
    }

    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        $fields = explode($encoding->fieldSeparator, $data);

        // The first token is the segment name, which is defined by the class.
        array_shift($fields);

        foreach ($fields as $idx => $field) {
            foreach (explode($encoding->repetitionSeparator, $field) as $rep => $value) {
                $this->getFieldRepetition($idx + 1, $rep)->parse($encoding, $value);
            }
        }
    }
}
