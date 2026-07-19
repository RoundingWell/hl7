<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Override;
use RoundingWell\HL7\Segment\MSH;

class GenericMessage extends AbstractMessage
{
    private string $name;
    private string $version;

    /** @var list<Segment> */
    private array $ordered = [];

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
     * Lines are processed in order so the original segment sequence is preserved even when a
     * repeating segment appears in non-contiguous clusters (e.g. OBX ... NTE ... OBX). Any name
     * not already registered (i.e. anything but MSH) is registered on the fly as a repeating
     * {@see GenericSegment}. The parse order is recorded so {@see serialize()} can reproduce it.
     */
    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        $this->ordered = [];

        foreach (explode($encoding->lineEnding, $data) as $line) {
            if ($line === '') {
                continue;
            }

            [$name] = explode($encoding->fieldSeparator, $line, 2);

            if (!in_array($name, $this->getNames(), true)) {
                $this->add($name, new StructureDefinition(GenericSegment::class, [$name], isRepeating: true));
            }

            $segment = $this->getRepetition($name, count($this->getAll($name)));

            assert($segment instanceof Segment, "Expected {$this->getName()}.{$name} to be a Segment");

            $segment->parse($encoding, $line);

            $this->ordered[] = $segment;
        }
    }

    #[Override]
    public function serialize(Encoding $encoding): string
    {
        // A hand-built message (never parsed) has no recorded order; fall back to schema-order walk.
        if ($this->ordered === []) {
            return parent::serialize($encoding);
        }

        return implode($encoding->lineEnding, array_map(static fn(Segment $segment): string => $segment->serialize(
            $encoding,
        ), $this->ordered));
    }
}
