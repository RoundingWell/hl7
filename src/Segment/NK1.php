<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\BaseField;
use RoundingWell\HL7\BaseSegment;
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

/**
 * Next of Kin / Associated Parties Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class NK1 extends BaseSegment
{
    public function __construct()
    {
        parent::__construct('NK1');

        $this->addField(1, new BaseField('Set ID', SI::class, required: true));
        $this->addField(2, new BaseField('Name', XPN::class, repeating: true));
        $this->addField(3, new BaseField('Relationship', CWE::class));
        $this->addField(4, new BaseField('Address', XAD::class, repeating: true));
        $this->addField(5, new BaseField('Phone Number', XTN::class, repeating: true));
        $this->addField(6, new BaseField('Business Phone Number', XTN::class, repeating: true));
        $this->addField(7, new BaseField('Contact Role', CWE::class));
        $this->addField(8, new BaseField('Start Date', DT::class));
        $this->addField(9, new BaseField('End Date', DT::class));
        $this->addField(10, new BaseField('Job Title', ST::class));
        $this->addField(11, new BaseField('Job Code/Class', JCC::class));
        $this->addField(12, new BaseField('Employee Number', CX::class));
        $this->addField(13, new BaseField('Organization Name', XON::class, repeating: true));
        $this->addField(14, new BaseField('Marital Status', CWE::class));
        $this->addField(15, new BaseField('Administrative Sex', CWE::class));
        $this->addField(16, new BaseField('Date/Time of Birth', DTM::class));
        $this->addField(17, new BaseField('Living Dependency', CWE::class, repeating: true));
        $this->addField(18, new BaseField('Ambulatory Status', CWE::class, repeating: true));
        $this->addField(19, new BaseField('Citizenship', CWE::class, repeating: true));
        $this->addField(20, new BaseField('Primary Language', CWE::class));
        $this->addField(21, new BaseField('Living Arrangement', CWE::class));
        $this->addField(22, new BaseField('Publicity Code', CWE::class));
        $this->addField(23, new BaseField('Protection Indicator', ID::class, args: ['table' => 136]));
        $this->addField(24, new BaseField('Student Indicator', CWE::class));
        $this->addField(25, new BaseField('Religion', CWE::class));
        $this->addField(26, new BaseField("Mother's Maiden Name", XPN::class, repeating: true));
        $this->addField(27, new BaseField('Nationality', CWE::class));
        $this->addField(28, new BaseField('Ethnic Group', CWE::class, repeating: true));
        $this->addField(29, new BaseField('Contact Reason', CWE::class, repeating: true));
        $this->addField(30, new BaseField("Contact Person's Name", XPN::class, repeating: true));
        $this->addField(31, new BaseField("Contact Person's Telephone Number", XTN::class, repeating: true));
        $this->addField(32, new BaseField("Contact Person's Address", XAD::class, repeating: true));
        $this->addField(33, new BaseField("Associated Party's Identifiers", CX::class, repeating: true));
        $this->addField(34, new BaseField('Job Status', CWE::class));
        $this->addField(35, new BaseField('Race', CWE::class, repeating: true));
        $this->addField(36, new BaseField('Handicap', CWE::class));
        $this->addField(37, new BaseField('Contact Person Social Security Number', ST::class));
        $this->addField(38, new BaseField('Next of Kin Birth Place', ST::class));
        $this->addField(39, new BaseField('VIP Indicator', CWE::class));
        $this->addField(40, new BaseField('Next of Kin Telecommunication Information', XTN::class));
        $this->addField(41, new BaseField("Contact Person's Telecommunication Information", XTN::class));
    }

    public function getIdentity(): SI
    {
        return $this->getField(1)->getInstance();
    }

    /**
     * @return list<XPN>
     */
    public function getName(): array
    {
        return $this->getField(2)->getInstance();
    }

    public function getRelationship(): CWE
    {
        return $this->getField(3)->getInstance();
    }

    /**
     * @return list<XAD>
     */
    public function getAddress(): array
    {
        return $this->getField(4)->getInstance();
    }

    /**
     * @return list<XTN>
     */
    public function getPhoneNumber(): array
    {
        return $this->getField(5)->getInstance();
    }

    /**
     * @return list<XTN>
     */
    public function getBusinessPhoneNumber(): array
    {
        return $this->getField(6)->getInstance();
    }

    public function getContactRole(): CWE
    {
        return $this->getField(7)->getInstance();
    }

    public function getStartDate(): DT
    {
        return $this->getField(8)->getInstance();
    }

    public function getEndDate(): DT
    {
        return $this->getField(9)->getInstance();
    }

    public function getJobTitle(): ST
    {
        return $this->getField(10)->getInstance();
    }

    public function getJobCode(): JCC
    {
        return $this->getField(11)->getInstance();
    }

    public function getEmployeeNumber(): CX
    {
        return $this->getField(12)->getInstance();
    }

    /**
     * @return list<XON>
     */
    public function getOrganizationName(): array
    {
        return $this->getField(13)->getInstance();
    }

    public function getMaritalStatus(): CWE
    {
        return $this->getField(14)->getInstance();
    }

    public function getAdministrativeSex(): CWE
    {
        return $this->getField(15)->getInstance();
    }

    public function getDateOfBirth(): DTM
    {
        return $this->getField(16)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getLivingDependency(): array
    {
        return $this->getField(17)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getAmbulatoryStatus(): array
    {
        return $this->getField(18)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getCitizenship(): array
    {
        return $this->getField(19)->getInstance();
    }

    public function getPrimaryLanguage(): CWE
    {
        return $this->getField(20)->getInstance();
    }

    public function getLivingArrangement(): CWE
    {
        return $this->getField(21)->getInstance();
    }

    public function getPublicityCode(): CWE
    {
        return $this->getField(22)->getInstance();
    }

    public function getProtectionIndicator(): ID
    {
        return $this->getField(23)->getInstance();
    }

    public function getStudentIndicator(): CWE
    {
        return $this->getField(24)->getInstance();
    }

    public function getReligion(): CWE
    {
        return $this->getField(25)->getInstance();
    }

    /**
     * @return list<XPN>
     */
    public function getMotherMaidenName(): array
    {
        return $this->getField(26)->getInstance();
    }

    public function getNationality(): CWE
    {
        return $this->getField(27)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getEthnicGroup(): array
    {
        return $this->getField(28)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getContactReason(): array
    {
        return $this->getField(29)->getInstance();
    }

    /**
     * @return list<XPN>
     */
    public function getContactPersonName(): array
    {
        return $this->getField(30)->getInstance();
    }

    /**
     * @return list<XTN>
     */
    public function getContactPersonPhoneNumber(): array
    {
        return $this->getField(31)->getInstance();
    }

    /**
     * @return list<XAD>
     */
    public function getContactPersonAddress(): array
    {
        return $this->getField(32)->getInstance();
    }

    /**
     * @return list<CX>
     */
    public function getAssociatedPartyIdentifiers(): array
    {
        return $this->getField(33)->getInstance();
    }

    public function getJobStatus(): CWE
    {
        return $this->getField(34)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getRace(): array
    {
        return $this->getField(35)->getInstance();
    }

    public function getHandicap(): CWE
    {
        return $this->getField(36)->getInstance();
    }

    public function getContactPersonSsnNumber(): ST
    {
        return $this->getField(37)->getInstance();
    }

    public function getBirthPlace(): ST
    {
        return $this->getField(38)->getInstance();
    }

    public function getVipIndicator(): CWE
    {
        return $this->getField(39)->getInstance();
    }

    public function getTelecommunicationInformation(): XTN
    {
        return $this->getField(40)->getInstance();
    }

    public function getContactPersonTelecommunicationInformation(): XTN
    {
        return $this->getField(41)->getInstance();
    }
}
