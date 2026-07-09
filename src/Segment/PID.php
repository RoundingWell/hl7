<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\BaseField;
use RoundingWell\HL7\BaseSegment;
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

/**
 * Patient Identification Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class PID extends BaseSegment
{
    public function __construct()
    {
        parent::__construct('PID');

        $this->addField(1, new BaseField('Set ID', SI::class));
        $this->addField(2, new BaseField('Patient ID', SI::class));
        $this->addField(3, new BaseField('Patient Identifier List', CX::class, repeating: true));
        $this->addField(4, new BaseField('Alternate Patient ID', SI::class));
        $this->addField(5, new BaseField('Patient Name', XPN::class, repeating: true));
        $this->addField(6, new BaseField("Mother's Maiden Name", XPN::class, repeating: true));
        $this->addField(7, new BaseField('Date/Time of Birth', DTM::class));
        $this->addField(8, new BaseField('Administrative Sex', CWE::class));
        $this->addField(9, new BaseField('Patient Alias', ST::class));
        $this->addField(10, new BaseField('Race', CWE::class, repeating: true));
        $this->addField(11, new BaseField('Patient Address', XAD::class, repeating: true));
        $this->addField(12, new BaseField('County Code', ST::class));
        $this->addField(13, new BaseField('Phone Number - Home', XTN::class, repeating: true));
        $this->addField(14, new BaseField('Phone Number - Business', XTN::class, repeating: true));
        $this->addField(15, new BaseField('Primary Language', CWE::class));
        $this->addField(16, new BaseField('Marital Status', CWE::class));
        $this->addField(17, new BaseField('Religion', CWE::class));
        $this->addField(18, new BaseField('Patient Account Number', CX::class));
        $this->addField(19, new BaseField('SSN Number - Patient', ST::class));
        $this->addField(20, new BaseField("Driver's License Number - Patient", ST::class));
        $this->addField(21, new BaseField("Mother's Identifier", CX::class, repeating: true));
        $this->addField(22, new BaseField('Ethnic Group', CWE::class, repeating: true));
        $this->addField(23, new BaseField('Birth Place', ST::class));
        $this->addField(24, new BaseField('Multiple Birth Indicator', ID::class, args: ['table' => 136]));
        $this->addField(25, new BaseField('Birth Order', NM::class));
        $this->addField(26, new BaseField('Citizenship', CWE::class, repeating: true));
        $this->addField(27, new BaseField('Veterans Military Status', CWE::class));
        $this->addField(28, new BaseField('Nationality', CWE::class));
        $this->addField(29, new BaseField('Patient Death Date and Time', DTM::class));
        $this->addField(30, new BaseField('Patient Death Indicator', ID::class, args: ['table' => 136]));
        $this->addField(31, new BaseField('Identity Unknown Indicator', ID::class, args: ['table' => 136]));
        $this->addField(32, new BaseField('Identity Reliability Code', CWE::class, repeating: true));
        $this->addField(33, new BaseField('Last Update Date/Time', DTM::class));
        $this->addField(34, new BaseField('Last Update Facility', HD::class));
        $this->addField(35, new BaseField('Taxonomic Classification Code', CWE::class));
        $this->addField(36, new BaseField('Breed Code', CWE::class));
        $this->addField(37, new BaseField('Strain', ST::class));
        $this->addField(38, new BaseField('Production Class Code', CWE::class));
        $this->addField(39, new BaseField('Tribal Citizenship', CWE::class, repeating: true));
        $this->addField(40, new BaseField('Patient Telecommunication Information', XTN::class, repeating: true));
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
