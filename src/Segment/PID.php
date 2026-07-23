<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\AbstractSegment;
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
use RoundingWell\HL7\TypeDefinition;

/**
 * Patient Identification Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class PID extends AbstractSegment
{
    public function __construct()
    {
        $this->add(new TypeDefinition('Set ID', SI::class, maxReps: 1));
        $this->add(new TypeDefinition('Patient ID', SI::class, maxReps: 1));
        $this->add(new TypeDefinition('Patient Identifier List', CX::class));
        $this->add(new TypeDefinition('Alternate Patient ID', SI::class, maxReps: 1));
        $this->add(new TypeDefinition('Patient Name', XPN::class));
        $this->add(new TypeDefinition("Mother's Maiden Name", XPN::class));
        $this->add(new TypeDefinition('Date/Time of Birth', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Administrative Sex', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Patient Alias', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Race', CWE::class));
        $this->add(new TypeDefinition('Patient Address', XAD::class));
        $this->add(new TypeDefinition('County Code', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Phone Number - Home', XTN::class));
        $this->add(new TypeDefinition('Phone Number - Business', XTN::class));
        $this->add(new TypeDefinition('Primary Language', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Marital Status', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Religion', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Patient Account Number', CX::class, maxReps: 1));
        $this->add(new TypeDefinition('SSN Number - Patient', ST::class, maxReps: 1));
        $this->add(new TypeDefinition("Driver's License Number - Patient", ST::class, maxReps: 1));
        $this->add(new TypeDefinition("Mother's Identifier", CX::class));
        $this->add(new TypeDefinition('Ethnic Group', CWE::class));
        $this->add(new TypeDefinition('Birth Place', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Multiple Birth Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Birth Order', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Citizenship', CWE::class));
        $this->add(new TypeDefinition('Veterans Military Status', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Nationality', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Patient Death Date and Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Patient Death Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Identity Unknown Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Identity Reliability Code', CWE::class));
        $this->add(new TypeDefinition('Last Update Date/Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Last Update Facility', HD::class, maxReps: 1));
        $this->add(new TypeDefinition('Taxonomic Classification Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Breed Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Strain', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Production Class Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Tribal Citizenship', CWE::class));
        $this->add(new TypeDefinition('Patient Telecommunication Information', XTN::class));
    }

    /**
     * PID.1 Set ID
     */
    public function getIdentity(): SI
    {
        return $this->getFieldRepetition(1, 0);
    }

    /**
     * PID.2 Patient ID
     */
    public function getPatientIdentity(): SI
    {
        return $this->getFieldRepetition(2, 0);
    }

    /**
     * PID.3 Patient Identifier List
     *
     * @return list<CX>
     */
    public function getIdentifierList(): array
    {
        return $this->getField(3);
    }

    /**
     * PID.4 Alternate Patient ID
     */
    public function getAlternateIdentity(): SI
    {
        return $this->getFieldRepetition(4, 0);
    }

    /**
     * PID.5 Patient Name
     *
     * @return list<XPN>
     */
    public function getPatientName(): array
    {
        return $this->getField(5);
    }

    /**
     * PID.6 Mother's Maiden Name
     *
     * @return list<XPN>
     */
    public function getMotherMaidenName(): array
    {
        return $this->getField(6);
    }

    /**
     * PID.7 Date/Time of Birth
     */
    public function getDateOfBirth(): DTM
    {
        return $this->getFieldRepetition(7, 0);
    }

    /**
     * PID.8 Administrative Sex
     */
    public function getAdministrativeSex(): CWE
    {
        return $this->getFieldRepetition(8, 0);
    }

    /**
     * PID.9 Patient Alias
     */
    public function getPatientAlias(): ST
    {
        return $this->getFieldRepetition(9, 0);
    }

    /**
     * PID.10 Race
     *
     * @return list<CWE>
     */
    public function getRace(): array
    {
        return $this->getField(10);
    }

    /**
     * PID.11 Patient Address
     *
     * @return list<XAD>
     */
    public function getPatientAddress(): array
    {
        return $this->getField(11);
    }

    /**
     * PID.12 County Code
     */
    public function getCountyCode(): ST
    {
        return $this->getFieldRepetition(12, 0);
    }

    /**
     * PID.13 Phone Number - Home
     *
     * @return list<XTN>
     */
    public function getPhoneNumberHome(): array
    {
        return $this->getField(13);
    }

    /**
     * PID.14 Phone Number - Business
     *
     * @return list<XTN>
     */
    public function getPhoneNumberBusiness(): array
    {
        return $this->getField(14);
    }

    /**
     * PID.15 Primary Language
     */
    public function getPrimaryLanguage(): CWE
    {
        return $this->getFieldRepetition(15, 0);
    }

    /**
     * PID.16 Marital Status
     */
    public function getMaritalStatus(): CWE
    {
        return $this->getFieldRepetition(16, 0);
    }

    /**
     * PID.17 Religion
     */
    public function getReligion(): CWE
    {
        return $this->getFieldRepetition(17, 0);
    }

    /**
     * PID.18 Patient Account Number
     */
    public function getAccountNumber(): CX
    {
        return $this->getFieldRepetition(18, 0);
    }

    /**
     * PID.19 SSN Number - Patient
     */
    public function getSsnNumber(): ST
    {
        return $this->getFieldRepetition(19, 0);
    }

    /**
     * PID.20 Driver's License Number - Patient
     */
    public function getDriverLicenseNumber(): ST
    {
        return $this->getFieldRepetition(20, 0);
    }

    /**
     * PID.21 Mother's Identifier
     *
     * @return list<CX>
     */
    public function getMotherIdentifier(): array
    {
        return $this->getField(21);
    }

    /**
     * PID.22 Ethnic Group
     *
     * @return list<CWE>
     */
    public function getEthnicGroup(): array
    {
        return $this->getField(22);
    }

    /**
     * PID.23 Birth Place
     */
    public function getBirthPlace(): ST
    {
        return $this->getFieldRepetition(23, 0);
    }

    /**
     * PID.24 Multiple Birth Indicator
     */
    public function getMultipleBirthIndicator(): ID
    {
        return $this->getFieldRepetition(24, 0);
    }

    /**
     * PID.25 Birth Order
     */
    public function getBirthOrder(): NM
    {
        return $this->getFieldRepetition(25, 0);
    }

    /**
     * PID.26 Citizenship
     *
     * @return list<CWE>
     */
    public function getCitizenship(): array
    {
        return $this->getField(26);
    }

    /**
     * PID.27 Veterans Military Status
     */
    public function getVeteransMilitaryStatus(): CWE
    {
        return $this->getFieldRepetition(27, 0);
    }

    /**
     * PID.28 Nationality
     */
    public function getNationality(): CWE
    {
        return $this->getFieldRepetition(28, 0);
    }

    /**
     * PID.29 Patient Death Date and Time
     */
    public function getPatientDeathDateAndTime(): DTM
    {
        return $this->getFieldRepetition(29, 0);
    }

    /**
     * PID.30 Patient Death Indicator
     */
    public function getPatientDeathIndicator(): ID
    {
        return $this->getFieldRepetition(30, 0);
    }

    /**
     * PID.31 Identity Unknown Indicator
     */
    public function getIdentityUnknownIndicator(): ID
    {
        return $this->getFieldRepetition(31, 0);
    }

    /**
     * PID.32 Identity Reliability Code
     *
     * @return list<CWE>
     */
    public function getIdentityReliabilityCode(): array
    {
        return $this->getField(32);
    }

    /**
     * PID.33 Last Update Date/Time
     */
    public function getLastUpdateDateTime(): DTM
    {
        return $this->getFieldRepetition(33, 0);
    }

    /**
     * PID.34 Last Update Facility
     */
    public function getLastUpdateFacility(): HD
    {
        return $this->getFieldRepetition(34, 0);
    }

    /**
     * PID.35 Taxonomic Classification Code
     */
    public function getTaxonomicClassificationCode(): CWE
    {
        return $this->getFieldRepetition(35, 0);
    }

    /**
     * PID.36 Breed Code
     */
    public function getBreedCode(): CWE
    {
        return $this->getFieldRepetition(36, 0);
    }

    /**
     * PID.37 Strain
     */
    public function getStrain(): ST
    {
        return $this->getFieldRepetition(37, 0);
    }

    /**
     * PID.38 Production Class Code
     */
    public function getProductionClassCode(): CWE
    {
        return $this->getFieldRepetition(38, 0);
    }

    /**
     * PID.39 Tribal Citizenship
     *
     * @return list<CWE>
     */
    public function getTribalCitizenship(): array
    {
        return $this->getField(39);
    }

    /**
     * PID.40 Patient Telecommunication Information
     *
     * @return list<XTN>
     */
    public function getPatientTelecommunicationInformation(): array
    {
        return $this->getField(40);
    }
}
