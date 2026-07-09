<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\NK1;

#[CoversClass(NK1::class)]
final class NK1Test extends TestCase
{
    private NK1 $nk1;

    #[Override]
    protected function setUp(): void
    {
        $this->nk1 = new NK1();
        $this->nk1->parse(new Encoding(), implode('|', [
            'NK1', // Segment name
            '1', // NK1.1 Set ID
            'DUCK^DONALD~DUCK^DAISY', // NK1.2 Name (repeating)
            'SPO^Spouse', // NK1.3 Relationship
            '111 DUCK ST^^FOWL^CA^99999~222 GOOSE LN^^FOWL^CA^88888', // NK1.4 Address (repeating)
            '8885551212~8885551213', // NK1.5 Phone Number (repeating)
            '8885552222', // NK1.6 Business Phone Number (repeating)
            'CP^Contact Person', // NK1.7 Contact Role
            '20050101', // NK1.8 Start Date
            '20051231', // NK1.9 End Date
            'Manager', // NK1.10 Job Title
            'ENG^Engineering', // NK1.11 Job Code/Class
            'EMP123^^^AccMgr^EN', // NK1.12 Employee Number
            'Acme Inc~Globex', // NK1.13 Organization Name (repeating)
            'M^Married', // NK1.14 Marital Status
            'F^Female', // NK1.15 Administrative Sex
            '19500101', // NK1.16 Date/Time of Birth
            'D1^Dependency~D2^Second', // NK1.17 Living Dependency (repeating)
            'A1^Ambulatory', // NK1.18 Ambulatory Status (repeating)
            'USA^United States', // NK1.19 Citizenship (repeating)
            'EN^English', // NK1.20 Primary Language
            'A^Alone', // NK1.21 Living Arrangement
            'PUB^Public', // NK1.22 Publicity Code
            'N', // NK1.23 Protection Indicator
            'Y^Student', // NK1.24 Student Indicator
            'CHR^Christian', // NK1.25 Religion
            'MOUSE^MINNIE', // NK1.26 Mother's Maiden Name (repeating)
            'US^American', // NK1.27 Nationality
            'H^Hispanic~N^NonHispanic', // NK1.28 Ethnic Group (repeating)
            'R1^Reason', // NK1.29 Contact Reason (repeating)
            'GOOF^GOOFY~PLUTO^PET', // NK1.30 Contact Person's Name (repeating)
            '8885553333', // NK1.31 Contact Person's Telephone Number (repeating)
            '333 DOG LN^^FOWL^CA^77777', // NK1.32 Contact Person's Address (repeating)
            'AP123^^^AccMgr^PI~AP456^^^AccMgr^PI', // NK1.33 Associated Party's Identifiers (repeating)
            'AC^Active', // NK1.34 Job Status
            '2106-3^White~1002-5^American', // NK1.35 Race (repeating)
            'HC^Handicap', // NK1.36 Handicap
            '123121234', // NK1.37 Contact Person Social Security Number
            'Duckburg', // NK1.38 Next of Kin Birth Place
            'VIP^Important', // NK1.39 VIP Indicator
            '8885554444', // NK1.40 Next of Kin Telecommunication Information
            '8885555555', // NK1.41 Contact Person's Telecommunication Information
        ]));
    }

    public function testIdentityMapsToItsValue(): void
    {
        // NK1.1 is the required sequence number that keeps repeated segments ordered.
        $this->assertSame('1', $this->nk1->getIdentity()->getValue());
    }

    public function testNamesCollectEachRepetition(): void
    {
        // NK1.2 is repeating; each associated party's name must be retained in order.
        $names = $this->nk1->getNextOfKinName();
        $this->assertCount(2, $names);
        $this->assertSame('DUCK', $names[0]->getFamilyName()->getSurname()->getValue());
        $this->assertSame('DONALD', $names[0]->getGivenName()->getValue());
        $this->assertSame('DAISY', $names[1]->getGivenName()->getValue());

        $maiden = $this->nk1->getMotherMaidenName();
        $this->assertSame('MOUSE', $maiden[0]->getFamilyName()->getSurname()->getValue());

        $contacts = $this->nk1->getContactPersonName();
        $this->assertCount(2, $contacts);
        $this->assertSame('GOOF', $contacts[0]->getFamilyName()->getSurname()->getValue());
        $this->assertSame('PLUTO', $contacts[1]->getFamilyName()->getSurname()->getValue());
    }

    public function testCodedFieldsMapToTheirLeadingIdentifier(): void
    {
        $this->assertSame('SPO', $this->nk1->getRelationship()->getIdentifier()->getValue());
        $this->assertSame('CP', $this->nk1->getContactRole()->getIdentifier()->getValue());
        $this->assertSame('M', $this->nk1->getMaritalStatus()->getIdentifier()->getValue());
        $this->assertSame('F', $this->nk1->getAdministrativeSex()->getIdentifier()->getValue());
        $this->assertSame('EN', $this->nk1->getPrimaryLanguage()->getIdentifier()->getValue());
        $this->assertSame('A', $this->nk1->getLivingArrangement()->getIdentifier()->getValue());
        $this->assertSame('PUB', $this->nk1->getPublicityCode()->getIdentifier()->getValue());
        $this->assertSame('Y', $this->nk1->getStudentIndicator()->getIdentifier()->getValue());
        $this->assertSame('CHR', $this->nk1->getReligion()->getIdentifier()->getValue());
        $this->assertSame('US', $this->nk1->getNationality()->getIdentifier()->getValue());
        $this->assertSame('AC', $this->nk1->getJobStatus()->getIdentifier()->getValue());
        $this->assertSame('HC', $this->nk1->getHandicap()->getIdentifier()->getValue());
        $this->assertSame('VIP', $this->nk1->getVipIndicator()->getIdentifier()->getValue());
    }

    public function testRepeatingCodedFieldsCollectEachEntry(): void
    {
        $dependency = $this->nk1->getLivingDependency();
        $this->assertCount(2, $dependency);
        $this->assertSame('D1', $dependency[0]->getIdentifier()->getValue());
        $this->assertSame('D2', $dependency[1]->getIdentifier()->getValue());

        $this->assertSame('A1', $this->nk1->getAmbulatoryStatus()[0]->getIdentifier()->getValue());
        $this->assertSame('USA', $this->nk1->getCitizenship()[0]->getIdentifier()->getValue());
        $this->assertSame('H', $this->nk1->getEthnicGroup()[0]->getIdentifier()->getValue());
        $this->assertSame('R1', $this->nk1->getContactReason()[0]->getIdentifier()->getValue());

        $race = $this->nk1->getRace();
        $this->assertCount(2, $race);
        $this->assertSame('2106-3', $race[0]->getIdentifier()->getValue());
        $this->assertSame('1002-5', $race[1]->getIdentifier()->getValue());
    }

    public function testAddressesCollectEachRepetition(): void
    {
        // NK1.4 and NK1.32 are repeating addresses with a nested street address component.
        $addresses = $this->nk1->getAddress();
        $this->assertCount(2, $addresses);
        $this->assertSame('111 DUCK ST', $addresses[0]->getStreetAddress()->getStreetAddress()->getValue());
        $this->assertSame('FOWL', $addresses[0]->getCity()->getValue());
        $this->assertSame('222 GOOSE LN', $addresses[1]->getStreetAddress()->getStreetAddress()->getValue());

        $contactAddresses = $this->nk1->getContactPersonAddress();
        $this->assertSame('333 DOG LN', $contactAddresses[0]->getStreetAddress()->getStreetAddress()->getValue());
    }

    public function testPhoneNumbersCollectEachRepetition(): void
    {
        $phones = $this->nk1->getPhoneNumber();
        $this->assertCount(2, $phones);
        $this->assertSame('8885551212', $phones[0]->getTelephoneNumber()->getValue());
        $this->assertSame('8885551213', $phones[1]->getTelephoneNumber()->getValue());

        $this->assertSame('8885552222', $this->nk1->getBusinessPhoneNumber()[0]->getTelephoneNumber()->getValue());
        $this->assertSame('8885553333', $this->nk1->getContactPersonPhoneNumber()[0]->getTelephoneNumber()->getValue());
        $this->assertSame(
            '8885554444',
            $this->nk1->getTelecommunicationInformation()->getTelephoneNumber()->getValue(),
        );
        $this->assertSame(
            '8885555555',
            $this->nk1->getContactPersonTelecommunicationInformation()->getTelephoneNumber()->getValue(),
        );
    }

    public function testDateFieldsMapToTheirValues(): void
    {
        $this->assertSame('20050101', $this->nk1->getStartDate()->getValue());
        $this->assertSame('20051231', $this->nk1->getEndDate()->getValue());
        $this->assertSame('19500101', $this->nk1->getDateOfBirth()->getValue());
    }

    public function testFreeTextFieldsMapToTheirValues(): void
    {
        $this->assertSame('Manager', $this->nk1->getJobTitle()->getValue());
        $this->assertSame('123121234', $this->nk1->getContactPersonSsnNumber()->getValue());
        $this->assertSame('Duckburg', $this->nk1->getBirthPlace()->getValue());
        $this->assertSame('N', $this->nk1->getProtectionIndicator()->getValue());
    }

    public function testJobCodeMapsToItsNestedComponent(): void
    {
        // NK1.11 nests a coded job code inside the JCC composite.
        $this->assertSame('ENG', $this->nk1->getJobCode()->getJobCode()->getIdentifier()->getValue());
    }

    public function testIdentifierFieldsMapToTheirComponents(): void
    {
        $employee = $this->nk1->getEmployeeNumber();
        $this->assertSame('EMP123', $employee->getId()->getValue());
        $this->assertSame('EN', $employee->getIdentifierTypeCode()->getValue());

        // NK1.33 is repeating; each associated-party identifier must be retained in order.
        $identifiers = $this->nk1->getAssociatedPartyIdentifiers();
        $this->assertCount(2, $identifiers);
        $this->assertSame('AP123', $identifiers[0]->getId()->getValue());
        $this->assertSame('AP456', $identifiers[1]->getId()->getValue());
    }

    public function testOrganizationNamesCollectEachRepetition(): void
    {
        // NK1.13 is repeating; each employing organization name must be retained.
        $organizations = $this->nk1->getOrganizationName();
        $this->assertCount(2, $organizations);
        $this->assertSame('Acme Inc', $organizations[0]->getOrganizationName()->getValue());
        $this->assertSame('Globex', $organizations[1]->getOrganizationName()->getValue());
    }
}
