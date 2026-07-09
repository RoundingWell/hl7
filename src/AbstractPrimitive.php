<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Override;
use ReflectionObject;

abstract class AbstractPrimitive implements Primitive
{
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
    }

    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        if ($data === '') {
            $this->clear();

            return;
        }

        $cs = $encoding->componentSeparator;
        $ss = $encoding->subcomponentSeparator;

        if (!str_contains($data, $cs) && !str_contains($data, $ss)) {
            $this->setValue($encoding->decode($data));

            return;
        }

        // Create a flattened array of components and subcomponents.
        $components = array_merge(...array_map(static fn(string $val) => explode($ss, $val), explode($cs, $data)));

        // The first component is the value of the primitive.
        $this->setValue($encoding->decode(array_shift($components)));

        // All other components are treated as extra subcomponents.
        foreach ($components as $val) {
            $this->extra->getComponent(count($this->extra))->parse($encoding, $val);
        }
    }
}
