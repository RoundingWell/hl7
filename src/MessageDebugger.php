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
final class MessageDebugger
{
    private const string INDENT = '  ';

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

        $label = "{$path} ({$field->getField()})";

        if ($field instanceof Composite) {
            $children = [];

            foreach ($field->getComponents() as $index => $component) {
                array_push($children, ...$this->field($component, $path . '.' . ($index + 1), $depth + 1));
            }

            return $this->withHeader($label, $children, $depth);
        }

        assert($field instanceof Primitive, 'A non-Varies type is either a composite or a primitive');

        $value = $field->getValue();

        return $value === '' ? [] : [$this->indent($depth) . $label . ': ' . $value];
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
