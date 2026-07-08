<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Person Location
 *
 * @mago-expect lint:excessive-parameter-list
 */
final readonly class PL implements Type
{
    use HasComponents;

    public function __construct(
        public HD $pointOfCare = new HD(),
        public HD $room = new HD(),
        public HD $bed = new HD(),
        public HD $facility = new HD(),
        public IS $locationStatus = new IS(306),
        public IS $personLocationType = new IS(305),
        public HD $building = new HD(),
        public HD $floor = new HD(),
        public ST $locationDescription = new ST(),
        public EI $comprehensiveLocationIdentifier = new EI(),
        public HD $assigningAuthorityForLocation = new HD(),
    ) {}
}
