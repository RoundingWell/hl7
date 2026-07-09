<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\TypeDefinition;

/**
 * Extended Address
 *
 * @mago-expect lint:too-many-methods
 */
final class XAD extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Street Address', SAD::class));
        $this->add(new TypeDefinition('Other Designation', ST::class));
        $this->add(new TypeDefinition('City', ST::class));
        $this->add(new TypeDefinition('State Or Province', ST::class));
        $this->add(new TypeDefinition('Zip Or Postal Code', ST::class));
        $this->add(new TypeDefinition('Country', ID::class, args: ['table' => 399]));
        $this->add(new TypeDefinition('Address Type', ID::class, args: ['table' => 190]));
        $this->add(new TypeDefinition('Other Geographic Designation', ST::class));
        $this->add(new TypeDefinition('County Parish Code', CWE::class));
        $this->add(new TypeDefinition('Census Tract', CWE::class));
        $this->add(new TypeDefinition('Address Representation Code', ID::class, args: ['table' => 465]));
        $this->add(new TypeDefinition('Address Validity Range', ST::class));
        $this->add(new TypeDefinition('Effective Date', DTM::class));
        $this->add(new TypeDefinition('Expiration Date', DTM::class));
        $this->add(new TypeDefinition('Expiration Reason', CWE::class));
        $this->add(new TypeDefinition('Temporary Indicator', ID::class, args: ['table' => 136]));
        $this->add(new TypeDefinition('Bad Address Indicator', ID::class, args: ['table' => 136]));
        $this->add(new TypeDefinition('Address Usage', ID::class, args: ['table' => 617]));
        $this->add(new TypeDefinition('Addressee', ST::class));
        $this->add(new TypeDefinition('Comment', ST::class));
        $this->add(new TypeDefinition('Preference Order', NM::class));
        $this->add(new TypeDefinition('Protection Code', CWE::class));
        $this->add(new TypeDefinition('Address Identifier', EI::class));
    }

    public function getStreetAddress(): SAD
    {
        return $this->getComponent(0);
    }

    public function getOtherDesignation(): ST
    {
        return $this->getComponent(1);
    }

    public function getCity(): ST
    {
        return $this->getComponent(2);
    }

    public function getStateOrProvince(): ST
    {
        return $this->getComponent(3);
    }

    public function getZipOrPostalCode(): ST
    {
        return $this->getComponent(4);
    }

    public function getCountry(): ID
    {
        return $this->getComponent(5);
    }

    public function getAddressType(): ID
    {
        return $this->getComponent(6);
    }

    public function getOtherGeographicDesignation(): ST
    {
        return $this->getComponent(7);
    }

    public function getCountyParishCode(): CWE
    {
        return $this->getComponent(8);
    }

    public function getCensusTract(): CWE
    {
        return $this->getComponent(9);
    }

    public function getAddressRepresentationCode(): ID
    {
        return $this->getComponent(10);
    }

    public function getAddressValidityRange(): ST
    {
        return $this->getComponent(11);
    }

    public function getEffectiveDate(): DTM
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

    public function getTemporaryIndicator(): ID
    {
        return $this->getComponent(15);
    }

    public function getBadAddressIndicator(): ID
    {
        return $this->getComponent(16);
    }

    public function getAddressUsage(): ID
    {
        return $this->getComponent(17);
    }

    public function getAddressee(): ST
    {
        return $this->getComponent(18);
    }

    public function getComment(): ST
    {
        return $this->getComponent(19);
    }

    public function getPreferenceOrder(): NM
    {
        return $this->getComponent(20);
    }

    public function getProtectionCode(): CWE
    {
        return $this->getComponent(21);
    }

    public function getAddressIdentifier(): EI
    {
        return $this->getComponent(22);
    }
}
