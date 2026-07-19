<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Override;
use ReflectionObject;

abstract class AbstractPrimitive implements Primitive
{
    use CanJoinElements;

    private string $value = '';

    private ExtraComponents $extra;

    public function __construct()
    {
        $this->extra = new ExtraComponents();
    }

    #[Override]
    public function getName(): string
    {
        return new ReflectionObject($this)->getShortName();
    }

    #[Override]
    public function getExtraComponents(): ExtraComponents
    {
        return $this->extra;
    }

    #[Override]
    public function getValue(): string
    {
        return $this->value;
    }

    #[Override]
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    #[Override]
    public function clear(): void
    {
        $this->value = '';
        $this->extra->clear();
    }

    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        $this->clear();

        if ($data === '') {
            return;
        }

        $ss = $encoding->subcomponentSeparator;

        // A primitive is the bottom of the separator hierarchy: its only structural split is
        // the subcomponent separator (&). A component separator (^) that reaches a primitive is
        // literal value data, so it is left untouched here.
        if (!str_contains($data, $ss)) {
            $this->setValue($encoding->decode($data));

            return;
        }

        $subcomponents = explode($ss, $data);

        // The first part is the value of the primitive.
        $this->setValue($encoding->decode(array_shift($subcomponents)));

        // Every remaining part is an extra subcomponent.
        foreach ($subcomponents as $val) {
            $this->extra->getComponent(count($this->extra))->parse($encoding, $val);
        }
    }

    #[Override]
    public function serialize(Encoding $encoding): string
    {
        $parts = [$encoding->encode($this->value)];

        foreach ($this->extra->getComponents() as $component) {
            $parts[] = $component->serialize($encoding);
        }

        return $this->joinTrimmed($parts, $encoding->subcomponentSeparator);
    }
}
