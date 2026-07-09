<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use OutOfBoundsException;
use Override;
use ReflectionObject;

abstract class AbstractComposite implements Composite
{
    use CanAssertNumbers;

    /** @var list<Type> */
    private array $components = [];

    /** @var list<TypeDefinition> */
    private array $definitions = [];

    private ExtraComponents $extra;

    public function __construct()
    {
        $this->extra = new ExtraComponents();
    }

    public function add(TypeDefinition $definition): void
    {
        $this->definitions[] = $definition;
    }

    #[Override]
    public function getName(): string
    {
        return new ReflectionObject($this)->getShortName();
    }

    #[Override]
    public function getComponent(int $number): Type
    {
        $this->assertNaturalNumber($number);

        if ($this->components[$number] ?? null) {
            return $this->components[$number];
        }

        return $this->components[$number] = $this->createComponent($number);
    }

    #[Override]
    public function getComponents(): array
    {
        $components = [];

        foreach (array_keys($this->definitions) as $number) {
            $components[] = $this->getComponent($number);
        }

        return $components;
    }

    private function createComponent(int $number): Type
    {
        $definition = $this->definitions[$number] ?? throw new OutOfBoundsException(
            "Component {$this->getName()}.{$number} is not defined",
        );

        return $definition->newInstance();
    }

    #[Override]
    public function getExtraComponents(): ExtraComponents
    {
        return $this->extra;
    }

    #[Override]
    public function clear(): void
    {
        $this->components = [];
    }

    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        $this->clear();

        match (true) {
            $data === '' => null,
            str_contains($data, $encoding->componentSeparator) => $this->parseComponents(
                $encoding,
                $data,
                $encoding->componentSeparator,
            ),
            str_contains($data, $encoding->subcomponentSeparator) => $this->parseComponents(
                $encoding,
                $data,
                $encoding->subcomponentSeparator,
            ),
            default => $this->parseComponents($encoding, $data, $encoding->componentSeparator),
        };
    }

    private function parseComponents(Encoding $encoding, string $data, string $delimiter): void
    {
        $overflow = count($this->definitions);

        foreach (explode($delimiter, $data) as $number => $value) {
            $component = $number >= $overflow
                ? $this->getExtraComponents()->getComponent($number - $overflow)
                : $this->getComponent($number);

            $component->parse($encoding, $value);
        }
    }
}
