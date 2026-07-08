<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Coded No Exceptions
 *
 * @mago-expect lint:excessive-parameter-list
 */
final readonly class CNE extends Composite
{
    public function __construct(
        public ST $identifier = new ST(),
        public ST $text = new ST(),
        public ID $codingSystem = new ID(396),
        public ST $alternateIdentifier = new ST(),
        public ST $alternateText = new ST(),
        public ID $alternateCodingSystem = new ID(396),
        public ST $codingSystemVersion = new ST(),
        public ST $alternateCodingSystemVersion = new ST(),
        public ST $originalText = new ST(),
        public ST $secondAlternateIdentifier = new ST(),
        public ST $secondAlternateText = new ST(),
        public ID $secondAlternateCodingSystem = new ID(396),
        public ST $secondAlternateCodingSystemVersion = new ST(),
        public ST $codingSystemOid = new ST(),
        public ST $valueSetOid = new ST(),
        public DTM $valueSetVersion = new DTM(),
        public ST $alternateCodingSystemOid = new ST(),
        public ST $alternateValueSetOid = new ST(),
        public DTM $alternateValueSetVersion = new DTM(),
        public ST $secondAlternateCodingSystemOid = new ST(),
        public ST $secondAlternateValueSetOid = new ST(),
        public DTM $secondAlternateValueSetVersion = new DTM(),
    ) {}
}
