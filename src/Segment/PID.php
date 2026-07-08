<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\CX;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\HD;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\SI;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XAD;
use RoundingWell\HL7\DataType\XPN;
use RoundingWell\HL7\DataType\XTN;
use RoundingWell\HL7\Field;
use RoundingWell\HL7\Segment;

/**
 * Patient Identification Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class PID extends Segment
{
    public function __construct()
    {
        parent::__construct('PID');

        $this->addField(1, new Field('Set ID', SI::class));
        $this->addField(2, new Field('Patient ID', SI::class));
        $this->addField(3, new Field('Patient Identifier List', CX::class, repeating: true));
        $this->addField(4, new Field('Alternate Patient ID', SI::class));
        $this->addField(5, new Field('Patient Name', XPN::class, repeating: true));
        $this->addField(6, new Field("Mother's Maiden Name", XPN::class, repeating: true));
        $this->addField(7, new Field('Date/Time of Birth', DTM::class));
        $this->addField(8, new Field('Administrative Sex', CWE::class));
        $this->addField(9, new Field('Patient Alias', ST::class));
        $this->addField(10, new Field('Race', CWE::class, repeating: true));
        $this->addField(11, new Field('Patient Address', XAD::class, repeating: true));
        $this->addField(12, new Field('County Code', ST::class));
        $this->addField(13, new Field('Phone Number - Home', XTN::class, repeating: true));
        $this->addField(14, new Field('Phone Number - Business', XTN::class, repeating: true));
        $this->addField(15, new Field('Primary Language', CWE::class));
        $this->addField(16, new Field('Marital Status', CWE::class));
        $this->addField(17, new Field('Religion', CWE::class));
        $this->addField(18, new Field('Patient Account Number', CX::class));
        $this->addField(19, new Field('SSN Number - Patient', ST::class));
        $this->addField(20, new Field("Driver's License Number - Patient", ST::class));
        $this->addField(21, new Field("Mother's Identifier", CX::class, repeating: true));
        $this->addField(22, new Field('Ethnic Group', CWE::class, repeating: true));
        $this->addField(23, new Field('Birth Place', ST::class));
        $this->addField(24, new Field('Multiple Birth Indicator', ID::class, args: ['table' => 136]));
        $this->addField(25, new Field('Birth Order', NM::class));
        $this->addField(26, new Field('Citizenship', CWE::class, repeating: true));
        $this->addField(27, new Field('Veterans Military Status', CWE::class));
        $this->addField(28, new Field('Nationality', CWE::class));
        $this->addField(29, new Field('Patient Death Date and Time', DTM::class));
        $this->addField(30, new Field('Patient Death Indicator', ID::class, args: ['table' => 136]));
        $this->addField(31, new Field('Identity Unknown Indicator', ID::class, args: ['table' => 136]));
        $this->addField(32, new Field('Identity Reliability Code', CWE::class, repeating: true));
        $this->addField(33, new Field('Last Update Date/Time', DTM::class));
        $this->addField(34, new Field('Last Update Facility', HD::class));
        $this->addField(35, new Field('Taxonomic Classification Code', CWE::class));
        $this->addField(36, new Field('Breed Code', CWE::class));
        $this->addField(37, new Field('Strain', ST::class));
        $this->addField(38, new Field('Production Class Code', CWE::class));
        $this->addField(39, new Field('Tribal Citizenship', CWE::class, repeating: true));
        $this->addField(40, new Field('Patient Telecommunication Information', XTN::class, repeating: true));
    }

    public function getIdentity(): SI
    {
        return $this->getField(1)->getInstance();
    }

    public function getPatientIdentity(): SI
    {
        return $this->getField(2)->getInstance();
    }

    /**
     * @return list<CX>
     */
    public function getIdentifierList(): array
    {
        return $this->getField(3)->getInstance();
    }

    public function getAlternateIdentity(): SI
    {
        return $this->getField(4)->getInstance();
    }

    /**
     * @return list<XPN>
     */
    public function getPatientName(): array
    {
        return $this->getField(5)->getInstance();
    }

    /**
     * @return list<XPN>
     */
    public function getMotherMaidenName(): array
    {
        return $this->getField(6)->getInstance();
    }

    public function getDateOfBirth(): DTM
    {
        return $this->getField(7)->getInstance();
    }

    public function getAdministrativeSex(): CWE
    {
        return $this->getField(8)->getInstance();
    }

    public function getPatientAlias(): ST
    {
        return $this->getField(9)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getRace(): array
    {
        return $this->getField(10)->getInstance();
    }

    /**
     * @return list<XAD>
     */
    public function getPatientAddress(): array
    {
        return $this->getField(11)->getInstance();
    }

    public function getCountyCode(): ST
    {
        return $this->getField(12)->getInstance();
    }

    /**
     * @return list<XTN>
     */
    public function getPhoneNumberHome(): array
    {
        return $this->getField(13)->getInstance();
    }

    /**
     * @return list<XTN>
     */
    public function getPhoneNumberBusiness(): array
    {
        return $this->getField(14)->getInstance();
    }

    public function getPrimaryLanguage(): CWE
    {
        return $this->getField(15)->getInstance();
    }

    public function getMaritalStatus(): CWE
    {
        return $this->getField(16)->getInstance();
    }

    public function getReligion(): CWE
    {
        return $this->getField(17)->getInstance();
    }

    public function getAccountNumber(): CX
    {
        return $this->getField(18)->getInstance();
    }

    public function getSsnNumber(): ST
    {
        return $this->getField(19)->getInstance();
    }

    public function getDriverLicenseNumber(): ST
    {
        return $this->getField(20)->getInstance();
    }

    /**
     * @return list<CX>
     */
    public function getMotherIdentifier(): array
    {
        return $this->getField(21)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getEthnicGroup(): array
    {
        return $this->getField(22)->getInstance();
    }

    public function getBirthPlace(): ST
    {
        return $this->getField(23)->getInstance();
    }

    public function getMultipleBirthIndicator(): ID
    {
        return $this->getField(24)->getInstance();
    }

    public function getBirthOrder(): NM
    {
        return $this->getField(25)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getCitizenship(): array
    {
        return $this->getField(26)->getInstance();
    }

    public function getVeteransMilitaryStatus(): CWE
    {
        return $this->getField(27)->getInstance();
    }

    public function getNationality(): CWE
    {
        return $this->getField(28)->getInstance();
    }

    public function getPatientDeathDateAndTime(): DTM
    {
        return $this->getField(29)->getInstance();
    }

    public function getPatientDeathIndicator(): ID
    {
        return $this->getField(30)->getInstance();
    }

    public function getIdentityUnknownIndicator(): ID
    {
        return $this->getField(31)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getIdentityReliabilityCode(): array
    {
        return $this->getField(32)->getInstance();
    }

    public function getLastUpdateDateTime(): DTM
    {
        return $this->getField(33)->getInstance();
    }

    public function getLastUpdateFacility(): HD
    {
        return $this->getField(34)->getInstance();
    }

    public function getTaxonomicClassificationCode(): CWE
    {
        return $this->getField(35)->getInstance();
    }

    public function getBreedCode(): CWE
    {
        return $this->getField(36)->getInstance();
    }

    public function getStrain(): ST
    {
        return $this->getField(37)->getInstance();
    }

    public function getProductionClassCode(): CWE
    {
        return $this->getField(38)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getTribalCitizenship(): array
    {
        return $this->getField(39)->getInstance();
    }

    /**
     * @return list<XTN>
     */
    public function getPatientTelecommunicationInformation(): array
    {
        return $this->getField(40)->getInstance();
    }
}
