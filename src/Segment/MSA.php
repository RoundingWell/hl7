<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\TypeDefinition;

/**
 * Message Acknowledgment Segment
 */
final class MSA extends AbstractSegment
{
    public function __construct()
    {
        $this->add(new TypeDefinition('Acknowledgment Code', ID::class, args: ['table' => 8], isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Message Control ID', ST::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Text Message', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Expected Sequence Number', NM::class, maxReps: 1));
    }

    public function getAcknowledgmentCode(): ID
    {
        return $this->getFieldRepetition(1, 0);
    }

    public function getMessageControlId(): ST
    {
        return $this->getFieldRepetition(2, 0);
    }

    public function getTextMessage(): ST
    {
        return $this->getFieldRepetition(3, 0);
    }

    public function getExpectedSequenceNumber(): NM
    {
        return $this->getFieldRepetition(4, 0);
    }
}
