<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use ReflectionObject;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidComponent;

/**
 * A {@see Type} composed of ordered components.
 *
 * Each component is itself a {@see Type}. A component that is another Composite
 * carries its parts as subcomponents, one delimiter level deeper.
 */
abstract readonly class Composite implements Type
{
    #[\Override]
    final public function setRaw(Encoding $encoding, string $value, int $depth = 0): void
    {
        $properties = $this->getTypedProperties();

        foreach ($this->splitComponents($encoding, $value, $depth) as $idx => $val) {
            $property = $properties[$idx] ?? null;

            if ($property === null) {
                throw InvalidComponent::notDefined(new ReflectionObject($this)->getShortName(), $idx + 1);
            }

            // Each level of nesting descends one delimiter: a component's own
            // parts are subcomponents, so children are set one depth deeper.
            $property->setRaw($encoding, $val, $depth + 1);
        }
    }

    /**
     * @return list<Type>
     */
    private function getTypedProperties(): array
    {
        // Only properties of Type can have their value set.
        $match = static fn(mixed $prop) => $prop instanceof Type;

        // @mago-expect analysis:less-specific-nested-return-statement
        return array_values(array_filter(get_object_vars($this), $match));
    }

    /**
     * The delimiter descends by depth: components at the top level, subcomponents within.
     *
     * @return list<string>
     */
    private function splitComponents(Encoding $encoding, string $data, int $depth): array
    {
        $separator = $depth === 0 ? $encoding->componentSeparator : $encoding->subcomponentSeparator;

        return explode($separator, $data);
    }
}
