<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use ReflectionObject;
use RoundingWell\HL7\Exception\InvalidSegment;
use RoundingWell\HL7\Segment\MSH;

readonly class BaseMessage
{
    final public function __construct(
        /** @var list<BaseSegment> */
        private array $segments,
    ) {}

    /**
     * @throws InvalidSegment if the MSH segment does not exist.
     */
    final public function getMSH(): MSH
    {
        // @mago-expect analysis:less-specific-return-statement
        return $this->getRequiredSegment('MSH');
    }

    final public function getSegment(string $id): ?BaseSegment
    {
        foreach ($this->segments as $segment) {
            if ($segment->getId() === $id) {
                return $segment;
            }
        }

        return null;
    }

    /**
     * @throws InvalidSegment when no segment with the given id is present.
     */
    final public function getRequiredSegment(string $id): BaseSegment
    {
        $segment = $this->getSegment($id);

        if ($segment === null) {
            throw InvalidSegment::notDefined(new ReflectionObject($this)->getShortName(), $id);
        }

        return $segment;
    }

    /**
     * @return list<BaseSegment>
     */
    final public function getAllSegments(string $id): array
    {
        $match = static fn(BaseSegment $segment): bool => $segment->getId() === $id;

        return array_values(array_filter($this->segments, $match));
    }
}
