<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

/**
 * Renders the populated structure of a message as an indented, human-readable tree
 *
 * Each level is indented two spaces: the message name at the root, its segments (and nested
 * groups) beneath it, and each populated field -- descending through composites to their
 * subcomponents -- beneath its segment. Every element is labelled with its access path and schema
 * name, e.g. "PID.5.1 (Family Name)". Elements with an empty value are omitted, so the output shows
 * only what the message actually carries.
 */
// @mago-expect lint:cyclomatic-complexity
final class MessageDebugger
{
    private const string INDENT = '  ';

    // The placeholder {@see CanHoldField} uses for an element that was never given a schema name.
    private const string NO_NAME = '<undefined>';

    public function describe(Message $message): string
    {
        return implode("\n", $this->group($message, 0));
    }

    /** @return list<string> */
    private function structure(Structure $structure, int $depth): array
    {
        if ($structure instanceof Group) {
            return $this->group($structure, $depth);
        }

        assert($structure instanceof Segment, 'A structure is either a group or a segment');

        return $this->segment($structure, $depth);
    }

    /** @return list<string> */
    private function group(Group $group, int $depth): array
    {
        $children = [];

        foreach ($group->getStructures() as $structure) {
            array_push($children, ...$this->structure($structure, $depth + 1));
        }

        return $this->withHeader($group->getName(), $children, $depth);
    }

    /** @return list<string> */
    private function segment(Segment $segment, int $depth): array
    {
        $children = [];

        for ($number = 1; $number <= $segment->getFieldCount(); $number++) {
            $repetitions = $segment->getField($number);
            $indexed = count($repetitions) > 1;

            foreach ($repetitions as $repetition => $field) {
                $path = $segment->getName() . '.' . $number . ($indexed ? "[{$repetition}]" : '');
                array_push($children, ...$this->field($field, $path, $depth + 1));
            }
        }

        return $this->withHeader($segment->getName(), $children, $depth);
    }

    /** @return list<string> */
    private function field(Type $field, string $path, int $depth): array
    {
        // Varies is a wrapper standing in for an undetermined type; describe the value it holds.
        if ($field instanceof Varies) {
            return $this->field($field->getData(), $path, $depth);
        }

        // An undeclared (generic) element has no schema name, so its "(name)" suffix is dropped.
        $name = $field->getField();
        $label = $name === '' || $name === self::NO_NAME ? $path : "{$path} ({$name})";

        if ($field instanceof Composite) {
            return $this->composite($field, $path, $label, $depth);
        }

        assert($field instanceof Primitive, 'A non-Varies type is either a composite or a primitive');

        return $this->primitive($field, $path, $label, $depth);
    }

    /** @return list<string> */
    private function composite(Composite $field, string $path, string $label, int $depth): array
    {
        $components = $field->getComponents();
        $extras = $field->getExtraComponents()->getComponents();

        // A generic field carries no declared components, so its data lives entirely in extras. A
        // single anonymous scalar component is effectively the field's value, so collapse it onto
        // the field line rather than emitting a lone ".1" child.
        if ($components === [] && count($extras) === 1 && $this->isScalar($extras[0])) {
            return $this->field($extras[0], $path, $depth);
        }

        // Declared components are numbered first; undeclared (extra) components continue after them,
        // so data a sender put beyond the schema is retained rather than dropped.
        $children = [];
        $number = 0;

        foreach ([...$components, ...$extras] as $component) {
            $number++;
            array_push($children, ...$this->field($component, "{$path}.{$number}", $depth + 1));
        }

        return $this->withHeader($label, $children, $depth);
    }

    /** @return list<string> */
    private function primitive(Primitive $field, string $path, string $label, int $depth): array
    {
        $value = $field->getValue();
        $extras = $field->getExtraComponents()->getComponents();

        // A plain primitive is a leaf: its value on the field line, dropped when empty.
        if ($extras === []) {
            return $value === '' ? [] : [$this->indent($depth) . $label . ': ' . $value];
        }

        // With extra subcomponents the value becomes subcomponent .1 and each extra follows, so the
        // empty slot of a leading "&d" is preserved and the surviving part keeps its true position.
        $children = $value === '' ? [] : [$this->indent($depth + 1) . "{$path}.1: {$value}"];
        $number = 1;

        foreach ($extras as $extra) {
            $number++;
            array_push($children, ...$this->field($extra, "{$path}.{$number}", $depth + 1));
        }

        return $this->withHeader($label, $children, $depth);
    }

    /**
     * A scalar is a bare value with no components or subcomponents beneath it -- the only shape that
     * can be safely flattened onto its parent's line without hiding nested data.
     */
    private function isScalar(Type $field): bool
    {
        if ($field instanceof Varies) {
            return $this->isScalar($field->getData());
        }

        return $field instanceof Primitive && $field->getExtraComponents()->getComponents() === [];
    }

    /**
     * Prefixes children with a header line, or drops the whole node when it has no children.
     *
     * A node with no populated descendants contributes nothing, so its header is never emitted --
     * this is what prunes empty segments, groups, and composites from the output.
     *
     * @param list<string> $children
     *
     * @return list<string>
     */
    private function withHeader(string $label, array $children, int $depth): array
    {
        if ($children === []) {
            return [];
        }

        return [$this->indent($depth) . $label, ...$children];
    }

    private function indent(int $depth): string
    {
        return str_repeat(self::INDENT, max(0, $depth));
    }
}
