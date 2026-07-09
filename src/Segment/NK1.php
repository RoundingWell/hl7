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

    public function getIdentity(): SI
    {
        return $this->getFieldRepetition(1, 0);
    }

    /**
     * @return list<XPN>
     */
    public function getNextOfKinName(): array
    {
        return $this->getField(2);
    }

    public function getRelationship(): CWE
    {
        return $this->getFieldRepetition(3, 0);
    }

    /**
     * @return list<XAD>
     */
    public function getAddress(): array
    {
        return $this->getField(4);
    }

    /**
     * @return list<XTN>
     */
    public function getPhoneNumber(): array
    {
        return $this->getField(5);
    }

    /**
     * @return list<XTN>
     */
    public function getBusinessPhoneNumber(): array
    {
        return $this->getField(6);
    }

    public function getContactRole(): CWE
    {
        return $this->getFieldRepetition(7, 0);
    }

    public function getStartDate(): DT
    {
        return $this->getFieldRepetition(8, 0);
    }

    public function getEndDate(): DT
    {
        return $this->getFieldRepetition(9, 0);
    }

    public function getJobTitle(): ST
    {
        return $this->getFieldRepetition(10, 0);
    }

    public function getJobCode(): JCC
    {
        return $this->getFieldRepetition(11, 0);
    }

    public function getEmployeeNumber(): CX
    {
        return $this->getFieldRepetition(12, 0);
    }

    /**
     * @return list<XON>
     */
    public function getOrganizationName(): array
    {
        return $this->getField(13);
    }

    public function getMaritalStatus(): CWE
    {
        return $this->getFieldRepetition(14, 0);
    }

    public function getAdministrativeSex(): CWE
    {
        return $this->getFieldRepetition(15, 0);
    }

    public function getDateOfBirth(): DTM
    {
        return $this->getFieldRepetition(16, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getLivingDependency(): array
    {
        return $this->getField(17);
    }

    /**
     * @return list<CWE>
     */
    public function getAmbulatoryStatus(): array
    {
        return $this->getField(18);
    }

    /**
     * @return list<CWE>
     */
    public function getCitizenship(): array
    {
        return $this->getField(19);
    }

    public function getPrimaryLanguage(): CWE
    {
        return $this->getFieldRepetition(20, 0);
    }

    public function getLivingArrangement(): CWE
    {
        return $this->getFieldRepetition(21, 0);
    }

    public function getPublicityCode(): CWE
    {
        return $this->getFieldRepetition(22, 0);
    }

    public function getProtectionIndicator(): ID
    {
        return $this->getFieldRepetition(23, 0);
    }

    public function getStudentIndicator(): CWE
    {
        return $this->getFieldRepetition(24, 0);
    }

    public function getReligion(): CWE
    {
        return $this->getFieldRepetition(25, 0);
    }

    /**
     * @return list<XPN>
     */
    public function getMotherMaidenName(): array
    {
        return $this->getField(26);
    }

    public function getNationality(): CWE
    {
        return $this->getFieldRepetition(27, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getEthnicGroup(): array
    {
        return $this->getField(28);
    }

    /**
     * @return list<CWE>
     */
    public function getContactReason(): array
    {
        return $this->getField(29);
    }

    /**
     * @return list<XPN>
     */
    public function getContactPersonName(): array
    {
        return $this->getField(30);
    }

    /**
     * @return list<XTN>
     */
    public function getContactPersonPhoneNumber(): array
    {
        return $this->getField(31);
    }

    /**
     * @return list<XAD>
     */
    public function getContactPersonAddress(): array
    {
        return $this->getField(32);
    }

    /**
     * @return list<CX>
     */
    public function getAssociatedPartyIdentifiers(): array
    {
        return $this->getField(33);
    }

    public function getJobStatus(): CWE
    {
        return $this->getFieldRepetition(34, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getRace(): array
    {
        return $this->getField(35);
    }

    public function getHandicap(): CWE
    {
        return $this->getFieldRepetition(36, 0);
    }

    public function getContactPersonSsnNumber(): ST
    {
        return $this->getFieldRepetition(37, 0);
    }

    public function getBirthPlace(): ST
    {
        return $this->getFieldRepetition(38, 0);
    }

    public function getVipIndicator(): CWE
    {
        return $this->getFieldRepetition(39, 0);
    }

    public function getTelecommunicationInformation(): XTN
    {
        return $this->getFieldRepetition(40, 0);
    }

    public function getContactPersonTelecommunicationInformation(): XTN
    {
        return $this->getFieldRepetition(41, 0);
    }
}
