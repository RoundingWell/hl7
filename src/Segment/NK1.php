<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\CX;
use RoundingWell\HL7\DataType\DT;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\JCC;
use RoundingWell\HL7\DataType\SI;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XAD;
use RoundingWell\HL7\DataType\XON;
use RoundingWell\HL7\DataType\XPN;
use RoundingWell\HL7\DataType\XTN;
use RoundingWell\HL7\TypeDefinition;

/**
 * Next of Kin / Associated Parties Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class NK1 extends AbstractSegment
{
    public function __construct()
    {
        $this->add(new TypeDefinition('Set ID', SI::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Name', XPN::class));
        $this->add(new TypeDefinition('Relationship', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Address', XAD::class));
        $this->add(new TypeDefinition('Phone Number', XTN::class));
        $this->add(new TypeDefinition('Business Phone Number', XTN::class));
        $this->add(new TypeDefinition('Contact Role', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Start Date', DT::class, maxReps: 1));
        $this->add(new TypeDefinition('End Date', DT::class, maxReps: 1));
        $this->add(new TypeDefinition('Job Title', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Job Code/Class', JCC::class, maxReps: 1));
        $this->add(new TypeDefinition('Employee Number', CX::class, maxReps: 1));
        $this->add(new TypeDefinition('Organization Name', XON::class));
        $this->add(new TypeDefinition('Marital Status', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Administrative Sex', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Date/Time of Birth', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Living Dependency', CWE::class));
        $this->add(new TypeDefinition('Ambulatory Status', CWE::class));
        $this->add(new TypeDefinition('Citizenship', CWE::class));
        $this->add(new TypeDefinition('Primary Language', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Living Arrangement', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Publicity Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Protection Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Student Indicator', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Religion', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition("Mother's Maiden Name", XPN::class));
        $this->add(new TypeDefinition('Nationality', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Ethnic Group', CWE::class));
        $this->add(new TypeDefinition('Contact Reason', CWE::class));
        $this->add(new TypeDefinition("Contact Person's Name", XPN::class));
        $this->add(new TypeDefinition("Contact Person's Telephone Number", XTN::class));
        $this->add(new TypeDefinition("Contact Person's Address", XAD::class));
        $this->add(new TypeDefinition("Associated Party's Identifiers", CX::class));
        $this->add(new TypeDefinition('Job Status', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Race', CWE::class));
        $this->add(new TypeDefinition('Handicap', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Contact Person Social Security Number', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Next of Kin Birth Place', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('VIP Indicator', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Next of Kin Telecommunication Information', XTN::class, maxReps: 1));
        $this->add(new TypeDefinition("Contact Person's Telecommunication Information", XTN::class, maxReps: 1));
    }

    /**
     * NK1.1 Set ID
     */
    public function getIdentity(): SI
    {
        return $this->getFieldRepetition(1, 0);
    }

    /**
     * NK1.2 Name
     *
     * @return list<XPN>
     */
    public function getNextOfKinName(): array
    {
        return $this->getField(2);
    }

    /**
     * NK1.3 Relationship
     */
    public function getRelationship(): CWE
    {
        return $this->getFieldRepetition(3, 0);
    }

    /**
     * NK1.4 Address
     *
     * @return list<XAD>
     */
    public function getAddress(): array
    {
        return $this->getField(4);
    }

    /**
     * NK1.5 Phone Number
     *
     * @return list<XTN>
     */
    public function getPhoneNumber(): array
    {
        return $this->getField(5);
    }

    /**
     * NK1.6 Business Phone Number
     *
     * @return list<XTN>
     */
    public function getBusinessPhoneNumber(): array
    {
        return $this->getField(6);
    }

    /**
     * NK1.7 Contact Role
     */
    public function getContactRole(): CWE
    {
        return $this->getFieldRepetition(7, 0);
    }

    /**
     * NK1.8 Start Date
     */
    public function getStartDate(): DT
    {
        return $this->getFieldRepetition(8, 0);
    }

    /**
     * NK1.9 End Date
     */
    public function getEndDate(): DT
    {
        return $this->getFieldRepetition(9, 0);
    }

    /**
     * NK1.10 Job Title
     */
    public function getJobTitle(): ST
    {
        return $this->getFieldRepetition(10, 0);
    }

    /**
     * NK1.11 Job Code/Class
     */
    public function getJobCode(): JCC
    {
        return $this->getFieldRepetition(11, 0);
    }

    /**
     * NK1.12 Employee Number
     */
    public function getEmployeeNumber(): CX
    {
        return $this->getFieldRepetition(12, 0);
    }

    /**
     * NK1.13 Organization Name
     *
     * @return list<XON>
     */
    public function getOrganizationName(): array
    {
        return $this->getField(13);
    }

    /**
     * NK1.14 Marital Status
     */
    public function getMaritalStatus(): CWE
    {
        return $this->getFieldRepetition(14, 0);
    }

    /**
     * NK1.15 Administrative Sex
     */
    public function getAdministrativeSex(): CWE
    {
        return $this->getFieldRepetition(15, 0);
    }

    /**
     * NK1.16 Date/Time of Birth
     */
    public function getDateOfBirth(): DTM
    {
        return $this->getFieldRepetition(16, 0);
    }

    /**
     * NK1.17 Living Dependency
     *
     * @return list<CWE>
     */
    public function getLivingDependency(): array
    {
        return $this->getField(17);
    }

    /**
     * NK1.18 Ambulatory Status
     *
     * @return list<CWE>
     */
    public function getAmbulatoryStatus(): array
    {
        return $this->getField(18);
    }

    /**
     * NK1.19 Citizenship
     *
     * @return list<CWE>
     */
    public function getCitizenship(): array
    {
        return $this->getField(19);
    }

    /**
     * NK1.20 Primary Language
     */
    public function getPrimaryLanguage(): CWE
    {
        return $this->getFieldRepetition(20, 0);
    }

    /**
     * NK1.21 Living Arrangement
     */
    public function getLivingArrangement(): CWE
    {
        return $this->getFieldRepetition(21, 0);
    }

    /**
     * NK1.22 Publicity Code
     */
    public function getPublicityCode(): CWE
    {
        return $this->getFieldRepetition(22, 0);
    }

    /**
     * NK1.23 Protection Indicator
     */
    public function getProtectionIndicator(): ID
    {
        return $this->getFieldRepetition(23, 0);
    }

    /**
     * NK1.24 Student Indicator
     */
    public function getStudentIndicator(): CWE
    {
        return $this->getFieldRepetition(24, 0);
    }

    /**
     * NK1.25 Religion
     */
    public function getReligion(): CWE
    {
        return $this->getFieldRepetition(25, 0);
    }

    /**
     * NK1.26 Mother's Maiden Name
     *
     * @return list<XPN>
     */
    public function getMotherMaidenName(): array
    {
        return $this->getField(26);
    }

    /**
     * NK1.27 Nationality
     */
    public function getNationality(): CWE
    {
        return $this->getFieldRepetition(27, 0);
    }

    /**
     * NK1.28 Ethnic Group
     *
     * @return list<CWE>
     */
    public function getEthnicGroup(): array
    {
        return $this->getField(28);
    }

    /**
     * NK1.29 Contact Reason
     *
     * @return list<CWE>
     */
    public function getContactReason(): array
    {
        return $this->getField(29);
    }

    /**
     * NK1.30 Contact Person's Name
     *
     * @return list<XPN>
     */
    public function getContactPersonName(): array
    {
        return $this->getField(30);
    }

    /**
     * NK1.31 Contact Person's Telephone Number
     *
     * @return list<XTN>
     */
    public function getContactPersonPhoneNumber(): array
    {
        return $this->getField(31);
    }

    /**
     * NK1.32 Contact Person's Address
     *
     * @return list<XAD>
     */
    public function getContactPersonAddress(): array
    {
        return $this->getField(32);
    }

    /**
     * NK1.33 Associated Party's Identifiers
     *
     * @return list<CX>
     */
    public function getAssociatedPartyIdentifiers(): array
    {
        return $this->getField(33);
    }

    /**
     * NK1.34 Job Status
     */
    public function getJobStatus(): CWE
    {
        return $this->getFieldRepetition(34, 0);
    }

    /**
     * NK1.35 Race
     *
     * @return list<CWE>
     */
    public function getRace(): array
    {
        return $this->getField(35);
    }

    /**
     * NK1.36 Handicap
     */
    public function getHandicap(): CWE
    {
        return $this->getFieldRepetition(36, 0);
    }

    /**
     * NK1.37 Contact Person Social Security Number
     */
    public function getContactPersonSsnNumber(): ST
    {
        return $this->getFieldRepetition(37, 0);
    }

    /**
     * NK1.38 Next of Kin Birth Place
     */
    public function getBirthPlace(): ST
    {
        return $this->getFieldRepetition(38, 0);
    }

    /**
     * NK1.39 VIP Indicator
     */
    public function getVipIndicator(): CWE
    {
        return $this->getFieldRepetition(39, 0);
    }

    /**
     * NK1.40 Next of Kin Telecommunication Information
     */
    public function getTelecommunicationInformation(): XTN
    {
        return $this->getFieldRepetition(40, 0);
    }

    /**
     * NK1.41 Contact Person's Telecommunication Information
     */
    public function getContactPersonTelecommunicationInformation(): XTN
    {
        return $this->getFieldRepetition(41, 0);
    }
}
