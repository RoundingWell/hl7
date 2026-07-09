<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\PID;

#[CoversClass(PID::class)]
final class PIDTest extends TestCase
{
    private PID $pid;

    #[Override]
    protected function setUp(): void
    {
        $this->pid = new PID();
        $this->pid->parse(new Encoding(), implode('|', [
            'PID', // Segment name
            '1', // PID.1 Set ID
            '2', // PID.2 Patient ID
            '10006579^^^AccMgr^MRN~99999^^^AccMgr^MR', // PID.3 Patient Identifier List (repeating)
            '4', // PID.4 Alternate Patient ID
            'DUCK^DONALD^D~MOUSE^MICKEY', // PID.5 Patient Name (repeating)
            'DUCK^DAISY', // PID.6 Mother's Maiden Name (repeating)
            '19241010', // PID.7 Date/Time of Birth
            'M^Male', // PID.8 Administrative Sex
            'DON', // PID.9 Patient Alias
            '2106-3^White~1002-5^American', // PID.10 Race (repeating)
            '111 DUCK ST^^FOWL^CA^999990000~222 GOOSE LN^^FOWL^CA^88888', // PID.11 Patient Address (repeating)
            'CountyX', // PID.12 County Code
            '8885551212~8885551213', // PID.13 Phone Number - Home (repeating)
            '8885551214', // PID.14 Phone Number - Business (repeating)
            'EN^English', // PID.15 Primary Language
            'M^Married', // PID.16 Marital Status
            'CHR^Christian', // PID.17 Religion
            '40007716^^^AccMgr^VN', // PID.18 Patient Account Number
            '123121234', // PID.19 SSN Number - Patient
            'DL12345', // PID.20 Driver's License Number - Patient
            '55555^^^Mom^MR', // PID.21 Mother's Identifier (repeating)
            'H^Hispanic', // PID.22 Ethnic Group (repeating)
            'Duckburg', // PID.23 Birth Place
            'N', // PID.24 Multiple Birth Indicator
            '1', // PID.25 Birth Order
            'USA^United States', // PID.26 Citizenship (repeating)
            'V^Veteran', // PID.27 Veterans Military Status
            'US^American', // PID.28 Nationality
            '20200101', // PID.29 Patient Death Date and Time
            'N', // PID.30 Patient Death Indicator
            'N', // PID.31 Identity Unknown Indicator
            'OK^Reliable', // PID.32 Identity Reliability Code (repeating)
            '20210101120000', // PID.33 Last Update Date/Time
            'FACILITY^^ISO', // PID.34 Last Update Facility
            'TAX^Taxonomy', // PID.35 Taxonomic Classification Code
            'BREED^Breed', // PID.36 Breed Code
            'StrainX', // PID.37 Strain
            'PROD^Production', // PID.38 Production Class Code
            'TRIBE^Tribal', // PID.39 Tribal Citizenship (repeating)
            '8885551215', // PID.40 Patient Telecommunication Information (repeating)
        ]));
    }

    public function testSequenceIdentifierFieldsMapToTheirValues(): void
    {
        $this->assertSame('1', $this->pid->getIdentity()->getValue());
        $this->assertSame('2', $this->pid->getPatientIdentity()->getValue());
        $this->assertSame('4', $this->pid->getAlternateIdentity()->getValue());
    }

    public function testIdentifierListsCollectEachRepetition(): void
    {
        // PID.3 and PID.21 are repeating identifiers; every entry must be retained in order.
        $identifiers = $this->pid->getIdentifierList();
        $this->assertCount(2, $identifiers);
        $this->assertSame('10006579', $identifiers[0]->getId()->getValue());
        $this->assertSame('MRN', $identifiers[0]->getIdentifierTypeCode()->getValue());
        $this->assertSame('99999', $identifiers[1]->getId()->getValue());

        $mother = $this->pid->getMotherIdentifier();
        $this->assertCount(1, $mother);
        $this->assertSame('55555', $mother[0]->getId()->getValue());
    }

    public function testNamesCollectEachRepetition(): void
    {
        // PID.5 and PID.6 are repeating names; the family name is a nested component.
        $names = $this->pid->getPatientName();
        $this->assertCount(2, $names);
        $this->assertSame('DUCK', $names[0]->getFamilyName()->getSurname()->getValue());
        $this->assertSame('DONALD', $names[0]->getGivenName()->getValue());
        $this->assertSame('MOUSE', $names[1]->getFamilyName()->getSurname()->getValue());

        $maiden = $this->pid->getMotherMaidenName();
        $this->assertCount(1, $maiden);
        $this->assertSame('DUCK', $maiden[0]->getFamilyName()->getSurname()->getValue());
    }

    public function testDateFieldsMapToTheirValues(): void
    {
        $this->assertSame('19241010', $this->pid->getDateOfBirth()->getValue());
        $this->assertSame('20200101', $this->pid->getPatientDeathDateAndTime()->getValue());
        $this->assertSame('20210101120000', $this->pid->getLastUpdateDateTime()->getValue());
    }

    public function testCodedFieldsMapToTheirLeadingIdentifier(): void
    {
        $this->assertSame('M', $this->pid->getAdministrativeSex()->getIdentifier()->getValue());
        $this->assertSame('EN', $this->pid->getPrimaryLanguage()->getIdentifier()->getValue());
        $this->assertSame('M', $this->pid->getMaritalStatus()->getIdentifier()->getValue());
        $this->assertSame('CHR', $this->pid->getReligion()->getIdentifier()->getValue());
        $this->assertSame('V', $this->pid->getVeteransMilitaryStatus()->getIdentifier()->getValue());
        $this->assertSame('US', $this->pid->getNationality()->getIdentifier()->getValue());
        $this->assertSame('TAX', $this->pid->getTaxonomicClassificationCode()->getIdentifier()->getValue());
        $this->assertSame('BREED', $this->pid->getBreedCode()->getIdentifier()->getValue());
        $this->assertSame('PROD', $this->pid->getProductionClassCode()->getIdentifier()->getValue());
    }

    public function testRepeatingCodedFieldsCollectEachEntry(): void
    {
        $race = $this->pid->getRace();
        $this->assertCount(2, $race);
        $this->assertSame('2106-3', $race[0]->getIdentifier()->getValue());
        $this->assertSame('1002-5', $race[1]->getIdentifier()->getValue());

        $this->assertSame('H', $this->pid->getEthnicGroup()[0]->getIdentifier()->getValue());
        $this->assertSame('USA', $this->pid->getCitizenship()[0]->getIdentifier()->getValue());
        $this->assertSame('OK', $this->pid->getIdentityReliabilityCode()[0]->getIdentifier()->getValue());
        $this->assertSame('TRIBE', $this->pid->getTribalCitizenship()[0]->getIdentifier()->getValue());
    }

    public function testFreeTextFieldsMapToTheirValues(): void
    {
        $this->assertSame('DON', $this->pid->getPatientAlias()->getValue());
        $this->assertSame('CountyX', $this->pid->getCountyCode()->getValue());
        $this->assertSame('123121234', $this->pid->getSsnNumber()->getValue());
        $this->assertSame('DL12345', $this->pid->getDriverLicenseNumber()->getValue());
        $this->assertSame('Duckburg', $this->pid->getBirthPlace()->getValue());
        $this->assertSame('StrainX', $this->pid->getStrain()->getValue());
    }

    public function testAddressesCollectEachRepetition(): void
    {
        // PID.11 is repeating; each address's leading component is a nested street address.
        $addresses = $this->pid->getPatientAddress();
        $this->assertCount(2, $addresses);
        $this->assertSame('111 DUCK ST', $addresses[0]->getStreetAddress()->getStreetAddress()->getValue());
        $this->assertSame('FOWL', $addresses[0]->getCity()->getValue());
        $this->assertSame('222 GOOSE LN', $addresses[1]->getStreetAddress()->getStreetAddress()->getValue());
    }

    public function testPhoneNumbersCollectEachRepetition(): void
    {
        $home = $this->pid->getPhoneNumberHome();
        $this->assertCount(2, $home);
        $this->assertSame('8885551212', $home[0]->getTelephoneNumber()->getValue());
        $this->assertSame('8885551213', $home[1]->getTelephoneNumber()->getValue());

        $this->assertSame('8885551214', $this->pid->getPhoneNumberBusiness()[0]->getTelephoneNumber()->getValue());
        $this->assertSame(
            '8885551215',
            $this->pid->getPatientTelecommunicationInformation()[0]->getTelephoneNumber()->getValue(),
        );
    }

    public function testAccountNumberMapsToItsComponents(): void
    {
        $account = $this->pid->getAccountNumber();
        $this->assertSame('40007716', $account->getId()->getValue());
        $this->assertSame('VN', $account->getIdentifierTypeCode()->getValue());
    }

    public function testIndicatorAndOrderFieldsMapToTheirValues(): void
    {
        $this->assertSame('N', $this->pid->getMultipleBirthIndicator()->getValue());
        $this->assertSame('1', $this->pid->getBirthOrder()->getValue());
        $this->assertSame('N', $this->pid->getPatientDeathIndicator()->getValue());
        $this->assertSame('N', $this->pid->getIdentityUnknownIndicator()->getValue());
    }

    public function testLastUpdateFacilityMapsToItsComponents(): void
    {
        $this->assertSame('FACILITY', $this->pid->getLastUpdateFacility()->getNamespaceId()->getValue());
        $this->assertSame('ISO', $this->pid->getLastUpdateFacility()->getUniversalIdType()->getValue());
    }
}
