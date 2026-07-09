<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Message Type
 */
final class MSG extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Message Type', ID::class, args: ['table' => 76]));
        $this->add(new TypeDefinition('Trigger Event', ID::class, args: ['table' => 77]));
        $this->add(new TypeDefinition('Message Structure', ID::class, args: ['table' => 78]));
    }

    public function getMessageType(): ID
    {
        return $this->getComponent(0);
    }

    public function getTriggerEvent(): ID
    {
        return $this->getComponent(1);
    }

    public function getMessageStructure(): ID
    {
        return $this->getComponent(2);
    }
}
