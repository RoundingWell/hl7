<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Extended Person Name
 *
 * @mago-expect lint:excessive-parameter-list
 */
final readonly class XPN extends Composite
{
    public function __construct(
        public FNx $familyName = new FNx(),
        public ST $givenName = new ST(),
        public ST $furtherGivenNames = new ST(),
        public ST $suffix = new ST(),
        public ST $prefix = new ST(),
        public ST $degree = new ST(),
        public ID $nameTypeCode = new ID(200),
        public ID $nameRepresentationCode = new ID(465),
        public CWE $nameContext = new CWE(),
        public ST $nameValidityRange = new ST(),
        public ID $nameAssemblyOrder = new ID(444),
        public DTM $effectiveDate = new DTM(),
        public DTM $expirationDate = new DTM(),
        public ST $professionalSuffix = new ST(),
        public ST $calledBy = new ST(),
    ) {}
}
