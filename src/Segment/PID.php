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

    public function getIdentity(): SI
    {
        return $this->getFieldRepetition(1, 0);
    }

    public function getPatientIdentity(): SI
    {
        return $this->getFieldRepetition(2, 0);
    }

    /**
     * @return list<CX>
     */
    public function getIdentifierList(): array
    {
        return $this->getField(3);
    }

    public function getAlternateIdentity(): SI
    {
        return $this->getFieldRepetition(4, 0);
    }

    /**
     * @return list<XPN>
     */
    public function getPatientName(): array
    {
        return $this->getField(5);
    }

    /**
     * @return list<XPN>
     */
    public function getMotherMaidenName(): array
    {
        return $this->getField(6);
    }

    public function getDateOfBirth(): DTM
    {
        return $this->getFieldRepetition(7, 0);
    }

    public function getAdministrativeSex(): CWE
    {
        return $this->getFieldRepetition(8, 0);
    }

    public function getPatientAlias(): ST
    {
        return $this->getFieldRepetition(9, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getRace(): array
    {
        return $this->getField(10);
    }

    /**
     * @return list<XAD>
     */
    public function getPatientAddress(): array
    {
        return $this->getField(11);
    }

    public function getCountyCode(): ST
    {
        return $this->getFieldRepetition(12, 0);
    }

    /**
     * @return list<XTN>
     */
    public function getPhoneNumberHome(): array
    {
        return $this->getField(13);
    }

    /**
     * @return list<XTN>
     */
    public function getPhoneNumberBusiness(): array
    {
        return $this->getField(14);
    }

    public function getPrimaryLanguage(): CWE
    {
        return $this->getFieldRepetition(15, 0);
    }

    public function getMaritalStatus(): CWE
    {
        return $this->getFieldRepetition(16, 0);
    }

    public function getReligion(): CWE
    {
        return $this->getFieldRepetition(17, 0);
    }

    public function getAccountNumber(): CX
    {
        return $this->getFieldRepetition(18, 0);
    }

    public function getSsnNumber(): ST
    {
        return $this->getFieldRepetition(19, 0);
    }

    public function getDriverLicenseNumber(): ST
    {
        return $this->getFieldRepetition(20, 0);
    }

    /**
     * @return list<CX>
     */
    public function getMotherIdentifier(): array
    {
        return $this->getField(21);
    }

    /**
     * @return list<CWE>
     */
    public function getEthnicGroup(): array
    {
        return $this->getField(22);
    }

    public function getBirthPlace(): ST
    {
        return $this->getFieldRepetition(23, 0);
    }

    public function getMultipleBirthIndicator(): ID
    {
        return $this->getFieldRepetition(24, 0);
    }

    public function getBirthOrder(): NM
    {
        return $this->getFieldRepetition(25, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getCitizenship(): array
    {
        return $this->getField(26);
    }

    public function getVeteransMilitaryStatus(): CWE
    {
        return $this->getFieldRepetition(27, 0);
    }

    public function getNationality(): CWE
    {
        return $this->getFieldRepetition(28, 0);
    }

    public function getPatientDeathDateAndTime(): DTM
    {
        return $this->getFieldRepetition(29, 0);
    }

    public function getPatientDeathIndicator(): ID
    {
        return $this->getFieldRepetition(30, 0);
    }

    public function getIdentityUnknownIndicator(): ID
    {
        return $this->getFieldRepetition(31, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getIdentityReliabilityCode(): array
    {
        return $this->getField(32);
    }

    public function getLastUpdateDateTime(): DTM
    {
        return $this->getFieldRepetition(33, 0);
    }

    public function getLastUpdateFacility(): HD
    {
        return $this->getFieldRepetition(34, 0);
    }

    public function getTaxonomicClassificationCode(): CWE
    {
        return $this->getFieldRepetition(35, 0);
    }

    public function getBreedCode(): CWE
    {
        return $this->getFieldRepetition(36, 0);
    }

    public function getStrain(): ST
    {
        return $this->getFieldRepetition(37, 0);
    }

    public function getProductionClassCode(): CWE
    {
        return $this->getFieldRepetition(38, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getTribalCitizenship(): array
    {
        return $this->getField(39);
    }

    /**
     * @return list<XTN>
     */
    public function getPatientTelecommunicationInformation(): array
    {
        return $this->getField(40);
    }
}
