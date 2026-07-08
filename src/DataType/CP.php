<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Composite Price
 *
 * @mago-expect lint:excessive-parameter-list
 */
final readonly class CP implements Type
{
    use HasComponents;

    public function __construct(
        public MO $price = new MO(),
        public ID $priceType = new ID(205),
        public NM $fromValue = new NM(),
        public NM $toValue = new NM(),
        public CWE $rangeUnits = new CWE(),
        public ID $rangeType = new ID(298),
    ) {}
}
