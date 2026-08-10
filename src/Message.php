<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Psr\Clock\ClockInterface;
use RoundingWell\HL7\Segment\MSH;

/**
 * An HL7 message
 */
interface Message extends Group
{
    public function getVersion(): string;

    public function getMSH(): MSH;

    /**
     * Returns the specific repetition of a segment
     *
     * If the segment does not already exist, it will be created.
     *
     * @param int $repetition zero or greater
     */
    public function getSegment(string $name, int $repetition): Segment;

    /**
     * Builds an ACK response to this message
     *
     * The acknowledgment swaps the sender/receiver, echoes this message's control ID into
     * MSA-2, and writes the acknowledgment code to MSA-1. The clock supplies MSH-7 and the
     * id generator supplies the acknowledgment's own MSH-10.
     */
    public function generateACK(AcknowledgmentCode $code, ClockInterface $clock, IdGenerator $idGenerator): Message;

    public function parse(Encoding $encoding, string $data): void;

    public function serialize(Encoding $encoding): string;

    /**
     * Returns an indented, human-readable dump of the message's populated structure
     *
     * Intended for debugging: it shows where each populated segment and field sits in the
     * hierarchy, labelling every element with its access path and schema name (e.g.
     * "PID.5.1 (Family Name)"). Empty elements are omitted.
     */
    public function debug(): string;
}
