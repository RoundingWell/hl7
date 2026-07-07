<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Coded Element
 *
 * @mago-expect lint:excessive-parameter-list
 */
final readonly class CE implements Type
{
    use HasComponents;

    public function __construct(
        public ST $identifier = new ST(),
        public ST $text = new ST(),
        public ID $codingSystem = new ID(396),
        public ST $alternateIdentifier = new ST(),
        public ST $alternateText = new ST(),
        public ID $alternateCodingSystem = new ID(396),
    ) {}
}
