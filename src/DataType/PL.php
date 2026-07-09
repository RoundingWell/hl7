<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Person Location
 *
 * @mago-expect lint:too-many-methods
 */
final class PL extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Point Of Care', HD::class));
        $this->add(new TypeDefinition('Room', HD::class));
        $this->add(new TypeDefinition('Bed', HD::class));
        $this->add(new TypeDefinition('Facility', HD::class));
        $this->add(new TypeDefinition('Location Status', IS::class, args: ['table' => 306]));
        $this->add(new TypeDefinition('Person Location Type', IS::class, args: ['table' => 305]));
        $this->add(new TypeDefinition('Building', HD::class));
        $this->add(new TypeDefinition('Floor', HD::class));
        $this->add(new TypeDefinition('Location Description', ST::class));
        $this->add(new TypeDefinition('Comprehensive Location Identifier', EI::class));
        $this->add(new TypeDefinition('Assigning Authority For Location', HD::class));
    }

    public function getPointOfCare(): HD
    {
        return $this->getComponent(0);
    }

    public function getRoom(): HD
    {
        return $this->getComponent(1);
    }

    public function getBed(): HD
    {
        return $this->getComponent(2);
    }

    public function getFacility(): HD
    {
        return $this->getComponent(3);
    }

    public function getLocationStatus(): IS
    {
        return $this->getComponent(4);
    }

    public function getPersonLocationType(): IS
    {
        return $this->getComponent(5);
    }

    public function getBuilding(): HD
    {
        return $this->getComponent(6);
    }

    public function getFloor(): HD
    {
        return $this->getComponent(7);
    }

    public function getLocationDescription(): ST
    {
        return $this->getComponent(8);
    }

    public function getComprehensiveLocationIdentifier(): EI
    {
        return $this->getComponent(9);
    }

    public function getAssigningAuthorityForLocation(): HD
    {
        return $this->getComponent(10);
    }
}
