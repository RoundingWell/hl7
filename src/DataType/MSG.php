<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Message Type
 */
final readonly class MSG implements Type
{
    use HasComponents;

    public function __construct(
        public ID $messageType = new ID(76),
        public ID $triggerEvent = new ID(77),
        public ID $messageStructure = new ID(78),
    ) {}
}
