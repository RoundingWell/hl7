<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Message;

use RoundingWell\HL7\AbstractMessage;
use RoundingWell\HL7\Segment\MSA;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\StructureDefinition;

/**
 * General Acknowledgment Message
 *
 * Segments:
 *
 * 1. MSH (Message Header)
 * 2. MSA (Message Acknowledgment)
 */
final class ACK extends AbstractMessage
{
    public function __construct()
    {
        $this->add('MSH', new StructureDefinition(MSH::class, isRequired: true));
        $this->add('MSA', new StructureDefinition(MSA::class, isRequired: true));
    }

    public function getMSA(): MSA
    {
        return $this->get('MSA');
    }
}
