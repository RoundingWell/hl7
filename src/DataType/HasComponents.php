<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use ReflectionObject;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidComponent;

trait HasComponents
{
    public function setRaw(Encoding $encoding, string $value): void
    {
        $properties = $this->getTypedProperties();

        foreach ($this->splitComponents($encoding, $value) as $idx => $val) {
            $property = $properties[$idx] ?? null;

            if ($property === null) {
                throw InvalidComponent::notDefined(new ReflectionObject($this)->getShortName(), $idx + 1);
            }

            $property->setRaw($encoding, $val);
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
     * @return list<string>
     */
    private function splitComponents(Encoding $encoding, string $data): array
    {
        return explode($encoding->componentSeparator, $data);
    }
}
