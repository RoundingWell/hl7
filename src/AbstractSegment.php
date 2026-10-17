<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use InvalidArgumentException;
use OutOfBoundsException;
use Override;
use ReflectionObject;
use Throwable;

use function array_key_exists;
use function array_map;
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
     * The line given to {@see parse()}, kept so fields are parsed only when they are read.
     */
    private ?string $raw = null;

    private ?Encoding $encoding = null;

    /**
     * Wire strings of the parsed line keyed by field number, or null until first needed.
     *
     * @var array<int, string>|null
     */
    private ?array $wire = null;

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
        $this->materialize($number);

        return $this->fields[$number - 1] ?? [];
    }

    #[Override]
    public function getFieldRepetition(int $number, int $repetition): Type
    {
        $this->assertPositiveNumber($number);
        $this->assertNaturalNumber($repetition);

        // Parse the field before the create-if-missing and bounds checks, so they see its wire
        // repetitions and throw the same messages as an eager parse would.
        $this->materialize($number);

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

        return $this->fields[$idx][$repetition] = $this->getDefinition($number)->newInstance();
    }

    /**
     * @param positive-int $number starting at 1
     */
    private function assertRepetitionAllowed(int $number, int $repetition): void
    {
        $total = $this->getLength($number);

        // Repetition indexes are 0-based, but the message reports the 1-based ordinal.
        $ordinal = $repetition + 1;

        if ($repetition > $total) {
            throw new OutOfBoundsException(
                "Cannot get repetition #{$ordinal} of {$this->getName()}.{$number}, there are only {$total} repetitions",
            );
        }

        $max = $this->maxRepetitions($number);

        if ($max !== 0 && $repetition >= $max) {
            throw new OutOfBoundsException(
                "Cannot get repetition #{$ordinal} of {$this->getName()}.{$number}, only {$max} "
                . ($max === 1 ? 'repetition is' : 'repetitions are')
                . ' allowed',
            );
        }
    }

    #[Override]
    public function getLength(int $number): int
    {
        $this->assertPositiveNumber($number);
        $this->materialize($number);

        return count($this->fields[$number - 1] ?? []);
    }

    #[Override]
    public function getNames(): array
    {
        $this->split();

        return array_map(static fn(TypeDefinition $definition): string => $definition->getName(), $this->definitions);
    }

    #[Override]
    public function getFieldCount(): int
    {
        $this->split();

        return count($this->definitions);
    }

    #[Override]
    public function isRequired(int $number): bool
    {
        $this->assertPositiveNumber($number);
        $this->split();

        return $this->getDefinition($number)->isRequired();
    }

    #[Override]
    public function maxRepetitions(int $number): int
    {
        $this->assertPositiveNumber($number);
        $this->split();

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

        $this->parse($encoding, $element->raw);

        return $offset + 1;
    }

    /**
     * Keeps the line and parses nothing: each field is parsed when it is first read, so a field
     * that is never read costs nothing and a malformed one throws only when read.
     */
    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        $this->fields = [];
        $this->raw = $data;
        $this->encoding = $encoding;
        $this->wire = null;
    }

    /**
     * Splits a line into the wire string of each field, keyed by field number.
     *
     * @return array<int, string>
     */
    protected function splitFields(Encoding $encoding, string $data): array
    {
        $wire = explode($encoding->fieldSeparator, $data);

        // The first token is the segment name, which is defined by the class; dropping it leaves
        // each field keyed by its number.
        unset($wire[0]);

        return $wire;
    }

    /**
     * Parses the wire string of one field into its repetitions.
     *
     * @param positive-int $number starting at 1
     */
    protected function parseField(Encoding $encoding, int $number, string $data): void
    {
        foreach (explode($encoding->repetitionSeparator, $data) as $rep => $value) {
            $this->getFieldRepetition($number, $rep)->parse($encoding, $value);
        }
    }

    /**
     * Splits the parsed line into wire strings on first use, without building any field.
     *
     * Wire fields beyond the declared ones get generic definitions, as an eager parse would have
     * added, so the field table reports them before any is read.
     *
     * @return array<int, string>
     */
    private function split(): array
    {
        if ($this->wire !== null) {
            return $this->wire;
        }

        $raw = $this->raw;
        $encoding = $this->encoding;
        $wire = $raw === null || $encoding === null ? [] : $this->splitFields($encoding, $raw);

        for ($m = max(count($wire) - count($this->definitions), 0); $m > 0; $m--) {
            $this->add(new TypeDefinition());
        }

        return $this->wire = $wire;
    }

    /**
     * Parses one field from its wire string, the first time it is read.
     *
     * @param positive-int $number starting at 1
     */
    private function materialize(int $number): void
    {
        $idx = $number - 1;
        $wire = $this->split();

        if ($this->encoding === null || array_key_exists($idx, $this->fields) || !array_key_exists($number, $wire)) {
            return;
        }

        // Mark the field read before parsing, so the repetitions parseField() creates do not
        // re-enter. A failed parse unmarks it: the field keeps no half-built state, and the next
        // read throws again.
        $this->fields[$idx] = [];

        try {
            $this->parseField($this->encoding, $number, $wire[$number]);
        } catch (Throwable $e) {
            unset($this->fields[$idx]);

            throw $e;
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
        // Treating the name and the serialized fields as trimmable parts means an empty fields
        // string (no non-empty fields) is dropped instead of leaving a dangling field separator.
        return $this->joinTrimmed([$this->getName(), $this->serializeFields($encoding, 1)], $encoding->fieldSeparator);
    }

    protected function serializeFields(Encoding $encoding, int $from): string
    {
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
}
