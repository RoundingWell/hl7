<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Override;
use RoundingWell\HL7\Segment\MSH;

class GenericMessage extends AbstractMessage
{
    use CanJoinElements;

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
        $segmentFactory = new SegmentFactory($encoding);

        // Any existing segments must be cleared and replaced.
        $this->ordered = [];

        foreach (explode($encoding->lineEnding, $data) as $line) {
            if ($line === '') {
                continue;
            }

            $segment = $segmentFactory->parse($line);

            // Determine the repetition number for this segment.
            $repetition = count($this->getAll($segment->getName()));

            // Insert the segment into the message at the determined repetition number.
            $this->setRepetition($segment->getName(), $repetition, $segment);

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

        $segments = array_map(static fn(Segment $segment) => $segment->serialize($encoding), $this->ordered);

        return $this->joinTrimmed($segments, $encoding->lineEnding);
    }
}
