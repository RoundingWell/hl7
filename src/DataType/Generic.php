<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidPath;

/**
 * Generic
 *
 * Holds data that cannot be parsed into a specific type.
 * The value is stored as an array with each element representing a value, component, or subcomponent.
 */
final class Generic implements Type
{
    /** @var list<string|list<string|list<string>>> */
    public array $value = [];

    /**
     * @param list<string|list<string|list<string>>> $value
     */
    public function setValue(array $value): void
    {
        $this->value = $value;
    }

    /**
     * @return list<string|list<string|list<string>>>
     */
    public function getValue(): array
    {
        return $this->value;
    }

    /**
     * Extract a part of the value using dot notation
     *
     * The path is a dot-separated string representing the index of each segment (1-based).
     *
     * For example:
     *
     *    $x = $field->getPath('1.2.8');
     *
     * Would be exactly the same as:
     *
     *    $x = $field->getValue()[0][1][7] ?? null;
     *
     * This method provides access in a more HL7-friendly way than {@see getValue()}.
     *
     * @return list<string|list<string|list<string>>>|string|null
     */
    public function getPath(string $path): array|string|null
    {
        if (!preg_match('/^\d+(\.\d+)*$/', $path)) {
            throw InvalidPath::notNumeric($path);
        }

        $value = $this->value;

        foreach (explode('.', $path) as $segment) {
            $segment = (int) $segment - 1;

            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return null;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    #[\Override]
    public function setRaw(Encoding $encoding, string $value, int $depth = 0): void
    {
        if ($value === '') {
            $this->setValue([]);

            return;
        }

        $value = [$value];

        // Split any components into a list of strings.
        array_walk_recursive($value, function (string &$value) use ($encoding) {
            // @mago-expect analysis:reference-constraint-violation
            $value = $this->splitComponents($encoding, $value);
        });

        // Split any subcomponents into a list of strings.
        array_walk_recursive($value, function (string &$value) use ($encoding) {
            // @mago-expect analysis:reference-constraint-violation
            $value = $this->splitSubcomponents($encoding, $value);
        });

        // Decode all values, components, and subcomponents.
        array_walk_recursive($value, static function (string &$value) use ($encoding) {
            $value = $encoding->decode($value);
        });

        // @mago-expect analysis:possibly-invalid-argument
        $this->setValue($value);
    }

    /**
     * @return string|list<string>
     */
    private function splitComponents(Encoding $encoding, string $value): array|string
    {
        if (!str_contains($value, $encoding->componentSeparator)) {
            return $value;
        }

        return explode($encoding->componentSeparator, $value);
    }

    /**
     * @return string|list<string>
     */
    private function splitSubcomponents(Encoding $encoding, string $value): array|string
    {
        if (!str_contains($value, $encoding->subcomponentSeparator)) {
            return $value;
        }

        return explode($encoding->subcomponentSeparator, $value);
    }
}
