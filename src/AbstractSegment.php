<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use InvalidArgumentException;
use OutOfBoundsException;
use Override;
use ReflectionObject;

use function array_map;
use function array_shift;
use function count;
use function explode;
use function max;

// @mago-expect lint:too-many-methods
// @mago-expect lint:cyclomatic-complexity
abstract class AbstractSegment implements Segment
{
    use CanAssertNumbers;
    use CanJoinElements;

    /** @var array<int, list<Type>> */
    private array $fields = [];

    /** @var list<TypeDefinition> */
    private array $definitions = [];

    /**
     * Wire line captured at bind time. Null once the segment is written, or when it was built
     * by hand and never bound. Serialization emits this verbatim while it is set.
     */
    private ?string $raw = null;

    private ?Encoding $boundEncoding = null;

    private bool $hydrated = false;

    private ?DirtyFlag $dirty = null;

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
        $this->hydrate();

        return $this->fields[$number - 1] ?? [];
    }

    #[Override]
    public function getFieldRepetition(int $number, int $repetition): Type
    {
        $this->assertPositiveNumber($number);
        $this->assertNaturalNumber($repetition);
        // Hydrate before the create-if-missing and bounds checks so a bound segment reports the
        // wire's repetitions, and so those checks still throw the same messages.
        $this->hydrate();

        // Field number is 1-based, but array indices are 0-based.
        $idx = $number - 1;

        if ($this->fields[$idx][$repetition] ?? null) {
            return $this->fields[$idx][$repetition];
        }

        // Add any fields that have not been defined, up to the requested number.
        for ($m = max($number - $this->getFieldCount(), 0); $m > 0; $m--) {
            $this->add(new TypeDefinition());
        }

        $this->assertRepetitionAllowed($number, $repetition);

        return $this->fields[$idx][$repetition] = $this->getDefinition($number)->newInstance($this->dirtyFlag());
    }

    /**
     * @param positive-int $number starting at 1
     */
    private function assertRepetitionAllowed(int $number, int $repetition): void
    {
        $total = $this->getLength($number);

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
    }

    #[Override]
    public function getLength(int $number): int
    {
        $this->assertPositiveNumber($number);
        $this->hydrate();

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
    public function firstNames(): array
    {
        return [$this->getName()];
    }

    #[Override]
    public function parseSegments(Encoding $encoding, array $segments, array $additionalNames, int $offset): int
    {
        $element = $segments[$offset] ?? throw new OutOfBoundsException(
            "Cannot parse {$this->getName()} at #{$offset}, the segment stream is exhausted",
        );

        $this->bind($encoding, $element->raw);

        return $offset + 1;
    }

    /**
     * Keep the wire line and parse its fields on first read or write.
     */
    public function bind(Encoding $encoding, string $data): void
    {
        $this->raw = $data;
        $this->boundEncoding = $encoding;
        $this->hydrated = false;
    }

    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        // An explicit parse owns the fields, so a previously bound line must not win serialize().
        $this->raw = null;
        $this->boundEncoding = null;
        $this->hydrated = true;
        $this->populate($encoding, $data);
    }

    protected function populate(Encoding $encoding, string $data): void
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

    #[Override]
    public function serializeLines(Encoding $encoding): array
    {
        return [$this->serialize($encoding)];
    }

    #[Override]
    public function serialize(Encoding $encoding): string
    {
        $raw = $this->unchangedRaw($encoding);

        if ($raw !== null) {
            return $raw;
        }

        // Treating the name and the serialized fields as trimmable parts means an empty fields
        // string (no non-empty fields) is dropped instead of leaving a dangling field separator.
        return $this->joinTrimmed([$this->getName(), $this->serializeFields($encoding, 1)], $encoding->fieldSeparator);
    }

    /**
     * The bound line, when it is still a valid encoding of this segment.
     *
     * A write drops the line. A different delimiter set cannot reuse it either; the line ending
     * is not part of the line and does not invalidate it.
     */
    protected function unchangedRaw(Encoding $encoding): ?string
    {
        $raw = $this->raw;
        $bound = $this->boundEncoding;

        if ($raw === null || $bound === null || !$bound->sameDelimiters($encoding)) {
            return null;
        }

        return $raw;
    }

    protected function serializeFields(Encoding $encoding, int $from): string
    {
        $this->hydrate();

        $fields = [];

        for ($number = $from; $number <= $this->getFieldCount(); $number++) {
            $repetitions = [];

            // @mago-expect analysis:possibly-invalid-argument
            foreach ($this->getField($number) as $repetition) {
                $repetitions[] = $repetition->serialize($encoding);
            }

            $fields[] = $this->joinTrimmed($repetitions, $encoding->repetitionSeparator);
        }

        return $this->joinTrimmed($fields, $encoding->fieldSeparator);
    }

    private function hydrate(): void
    {
        $raw = $this->raw;
        $encoding = $this->boundEncoding;

        if ($this->hydrated || $raw === null || $encoding === null) {
            return;
        }

        // Drop the line before parsing so field access inside populate cannot re-enter, and so
        // the writes parse itself performs do not stick. Restoring afterwards is what makes a
        // read keep the byte-exact line.
        $this->raw = null;
        $this->populate($encoding, $raw);
        $this->raw = $raw;
        $this->hydrated = true;
    }

    private function dirtyFlag(): DirtyFlag
    {
        return $this->dirty ??= new DirtyFlag(function (): void {
            $this->raw = null;
        });
    }
}
