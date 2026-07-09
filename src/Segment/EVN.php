<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\BaseField;
use RoundingWell\HL7\BaseSegment;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\HD;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XCN;

/**
 * Event Type Segment
 */
final class EVN extends BaseSegment
{
    public function __construct()
    {
        parent::__construct('EVN');

        $this->addField(1, new BaseField('Event Type Code', ST::class));
        $this->addField(2, new BaseField('Recorded Date/Time', DTM::class, required: true));
        $this->addField(3, new BaseField('Date/Time Planned Event', DTM::class));
        $this->addField(4, new BaseField('Event Reason Code', CWE::class));
        $this->addField(5, new BaseField('Operator ID', XCN::class, repeating: true));
        $this->addField(6, new BaseField('Event Occurred', DTM::class));
        $this->addField(7, new BaseField('Event Facility', HD::class));
    }

    public function getTypeCode(): ST
    {
        return $this->getField(1)->getInstance();
    }

    public function getRecordedDateTime(): DTM
    {
        return $this->getField(2)->getInstance();
    }

    public function getPlannedDateTime(): DTM
    {
        return $this->getField(3)->getInstance();
    }

    public function getEventReasonCode(): CWE
    {
        return $this->getField(4)->getInstance();
    }

    /**
     * @return list<XCN>
     */
    public function getOperatorId(): array
    {
        return $this->getField(5)->getInstance();
    }

    public function getOccurredDateTime(): DTM
    {
        return $this->getField(6)->getInstance();
    }

    public function getEventFacility(): HD
    {
        return $this->getField(7)->getInstance();
    }
}
