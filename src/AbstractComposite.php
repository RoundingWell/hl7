<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use OutOfBoundsException;
use Override;
use ReflectionObject;

abstract class AbstractComposite implements Composite
{
    use CanAssertNumbers;
    use CanHoldField;
    use CanJoinElements;

    /** @var list<Type> */
    private array $components = [];

    /** @var list<TypeDefinition> */
    private array $definitions = [];

    /**
     * Whether this composite is nested as a component of another composite.
     *
     * Depth fixes the separator: a field-level composite splits its input on the component
     * separator, but a nested composite sits one level down, so its own parts arrive as
     * subcomponents and it splits on the subcomponent separator instead.
     */
    private bool $nested = false;

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

        $component = $definition->newInstance();

        // A composite occupying a component slot is nested one level down, so it parses its parts
        // as subcomponents. Field-level composites are created by the segment, never here, so
        // they are never marked nested.
        if ($component instanceof self) {
            $component->nested = true;
        }

        return $component;
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
        $this->extra->clear();
    }

    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        $this->clear();

        if ($data === '') {
            return;
        }

        // Depth is fixed by the separator hierarchy, not inferred from content. A field-level
        // composite splits on the component separator; a nested composite splits on the
        // subcomponent separator (see $nested). A component that carries a "&" it should keep is
        // handled by that component's own parse(), which peels off subcomponents.
        $separator = $this->nested ? $encoding->subcomponentSeparator : $encoding->componentSeparator;

        $this->parseComponents($encoding, $separator, $data);
    }

    private function parseComponents(Encoding $encoding, string $separator, string $data): void
    {
        $overflow = count($this->definitions);

        foreach (explode($separator, $data) as $number => $value) {
            $component = $number >= $overflow
                ? $this->getExtraComponents()->getComponent($number - $overflow)
                : $this->getComponent($number);

            $component->parse($encoding, $value);
        }
    }

    #[Override]
    public function serialize(Encoding $encoding): string
    {
        // Same depth rule as parse(): a field-level composite joins components with the component
        // separator, a nested composite joins its parts with the subcomponent separator.
        $separator = $this->nested ? $encoding->subcomponentSeparator : $encoding->componentSeparator;

        $parts = [];

        foreach ($this->getComponents() as $component) {
            $parts[] = $component->serialize($encoding);
        }

        foreach ($this->extra->getComponents() as $component) {
            $parts[] = $component->serialize($encoding);
        }

        return $this->joinTrimmed($parts, $separator);
    }
}
