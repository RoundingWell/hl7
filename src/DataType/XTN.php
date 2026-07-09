<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Extended Telecommunication Number
 *
 * @mago-expect lint:too-many-methods
 */
final class XTN extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Telephone Number', ST::class));
        $this->add(new TypeDefinition('Telecommunication Use Code', ST::class));
        $this->add(new TypeDefinition('Telecommunication Equipment Type', ST::class));
        $this->add(new TypeDefinition('Communication Address', ST::class));
        $this->add(new TypeDefinition('Country Code', SNM::class));
        $this->add(new TypeDefinition('Area City Code', SNM::class));
        $this->add(new TypeDefinition('Local Number', SNM::class));
        $this->add(new TypeDefinition('Extension', SNM::class));
        $this->add(new TypeDefinition('Any Text', ST::class));
        $this->add(new TypeDefinition('Extension Prefix', ST::class));
        $this->add(new TypeDefinition('Speed Dial Code', ST::class));
        $this->add(new TypeDefinition('Unformatted Telephone Number', ST::class));
        $this->add(new TypeDefinition('Effective Start Date', DTM::class));
        $this->add(new TypeDefinition('Expiration Date', DTM::class));
        $this->add(new TypeDefinition('Expiration Reason', CWE::class));
        $this->add(new TypeDefinition('Protection Code', CWE::class));
        $this->add(new TypeDefinition('Shared Telecommunication Identifier', EI::class));
        $this->add(new TypeDefinition('Preference Order', NM::class));
    }

    public function getTelephoneNumber(): ST
    {
        return $this->getComponent(0);
    }

    public function getTelecommunicationUseCode(): ST
    {
        return $this->getComponent(1);
    }

    public function getTelecommunicationEquipmentType(): ST
    {
        return $this->getComponent(2);
    }

    public function getCommunicationAddress(): ST
    {
        return $this->getComponent(3);
    }

    public function getCountryCode(): SNM
    {
        return $this->getComponent(4);
    }

    public function getAreaCityCode(): SNM
    {
        return $this->getComponent(5);
    }

    public function getLocalNumber(): SNM
    {
        return $this->getComponent(6);
    }

    public function getExtension(): SNM
    {
        return $this->getComponent(7);
    }

    public function getAnyText(): ST
    {
        return $this->getComponent(8);
    }

    public function getExtensionPrefix(): ST
    {
        return $this->getComponent(9);
    }

    public function getSpeedDialCode(): ST
    {
        return $this->getComponent(10);
    }

    public function getUnformattedTelephoneNumber(): ST
    {
        return $this->getComponent(11);
    }

    public function getEffectiveStartDate(): DTM
    {
        return $this->getComponent(12);
    }

    public function getExpirationDate(): DTM
    {
        return $this->getComponent(13);
    }

    public function getExpirationReason(): CWE
    {
        return $this->getComponent(14);
    }

    public function getProtectionCode(): CWE
    {
        return $this->getComponent(15);
    }

    public function getSharedTelecommunicationIdentifier(): EI
    {
        return $this->getComponent(16);
    }

    public function getPreferenceOrder(): NM
    {
        return $this->getComponent(17);
    }
}
