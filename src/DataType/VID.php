<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Version Identifier
 */
final readonly class VID extends Composite
{
    public function __construct(
        public ID $id = new ID(104),
        public CE $i18nCode = new CE(),
        public CE $i18nVersion = new CE(),
    ) {}
}
