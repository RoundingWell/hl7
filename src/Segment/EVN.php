<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\HD;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XCN;
use RoundingWell\HL7\TypeDefinition;

/**
 * Event Type Segment
 */
final class EVN extends AbstractSegment
{
    public function __construct()
    {
        $this->add(new TypeDefinition('Event Type Code', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Recorded Date/Time', DTM::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Date/Time Planned Event', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Event Reason Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Operator ID', XCN::class));
        $this->add(new TypeDefinition('Event Occurred', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Event Facility', HD::class, maxReps: 1));
    }

    /**
     * EVN.1 Event Type Code
     */
    public function getTypeCode(): ST
    {
        return $this->getFieldRepetition(1, 0);
    }

    /**
     * EVN.2 Recorded Date/Time
     */
    public function getRecordedDateTime(): DTM
    {
        return $this->getFieldRepetition(2, 0);
    }

    /**
     * EVN.3 Date/Time Planned Event
     */
    public function getPlannedDateTime(): DTM
    {
        return $this->getFieldRepetition(3, 0);
    }

    /**
     * EVN.4 Event Reason Code
     */
    public function getEventReasonCode(): CWE
    {
        return $this->getFieldRepetition(4, 0);
    }

    /**
     * EVN.5 Operator ID
     *
     * @return list<XCN>
     */
    public function getOperatorId(): array
    {
        return $this->getField(5);
    }

    /**
     * EVN.6 Event Occurred
     */
    public function getOccurredDateTime(): DTM
    {
        return $this->getFieldRepetition(6, 0);
    }

    /**
     * EVN.7 Event Facility
     */
    public function getEventFacility(): HD
    {
        return $this->getFieldRepetition(7, 0);
    }
}
