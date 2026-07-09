<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Override;
use RoundingWell\HL7\Segment\MSH;

class GenericMessage extends AbstractMessage
{
    private string $name;
    private string $version;

    public function __construct(string $name, string $version)
    {
        $this->name = $name;
        $this->version = $version;

        $this->add('MSH', new StructureDefinition(MSH::class));
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    public function getVersion(): string
    {
        return $this->version;
    }

    #[Override]
    public function getMSH(): MSH
    {
        $msh = $this->getRepetition('MSH', 0);

        assert($msh instanceof MSH, "Expected {$this->getName()}.MSH to be an MSH segment");

        return $msh;
    }

    /**
     * Parses arbitrary HL7 messages, tolerating segments this class has no model for.
     *
     * Segments are grouped by name (order-independent) so that a repeating segment which
     * appears in non-contiguous clusters (e.g. OBX ... NTE ... OBX) keeps every occurrence.
     * Any name not already registered (i.e. anything but MSH) is registered on the fly as a
     * repeating {@see GenericSegment}, preserving the tolerance the old BaseMessage container offered.
     */
    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        foreach ($this->groupByName($encoding, $data) as $name => $lines) {
            if (!in_array($name, $this->getNames(), true)) {
                $this->add($name, new StructureDefinition(GenericSegment::class, [$name], isRepeating: true));
            }

            foreach ($lines as $repetition => $line) {
                $segment = $this->getRepetition($name, $repetition);

                assert(
                    $segment instanceof Segment,
                    "Expected {$this->getName()}.{$name}.{$repetition} to be a Segment",
                );

                $segment->parse($encoding, $line);
            }
        }
    }

    /**
     * Groups raw segment lines by segment name, preserving repetition order within each name.
     *
     * @return array<string, list<string>>
     */
    private function groupByName(Encoding $encoding, string $data): array
    {
        $segments = [];

        foreach (explode($encoding->lineEnding, $data) as $line) {
            if ($line === '') {
                continue;
            }

            [$name] = explode($encoding->fieldSeparator, $line, 2);

            $segments[$name][] = $line;
        }

        return $segments;
    }
}
