<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use RoundingWell\HL7\Segment\MSH;

/**
 * An HL7 message
 */
interface Message extends Group
{
    public function getVersion(): string;

    public function getMSH(): MSH;

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
