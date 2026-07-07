<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Family Name
 */
final readonly class FNx implements Type
{
    use HasComponents;

    public function __construct(
        public ST $surname = new ST(),
        public ST $ownSurnamePrefix = new ST(),
        public ST $ownSurname = new ST(),
        public ST $surnamePrefixFromPartnerSpouse = new ST(),
        public ST $surnameFromPartnerSpouse = new ST(),
    ) {}
}
