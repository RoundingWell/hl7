<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\PID;

/**
 * @mago-expect lint:too-many-methods
 */
#[CoversClass(PID::class)]
final class PIDTest extends TestCase
{
    private PID $pid;

    #[Override]
    protected function setUp(): void
    {
        $this->pid = new PID();
        $this->pid->setRaw(new Encoding(), [
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
        ]);
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
        $this->assertSame('10006579', $identifiers[0]->id->getValue());
        $this->assertSame('MRN', $identifiers[0]->identifierTypeCode->getValue());
        $this->assertSame('99999', $identifiers[1]->id->getValue());

        $mother = $this->pid->getMotherIdentifier();
        $this->assertCount(1, $mother);
        $this->assertSame('55555', $mother[0]->id->getValue());
    }

    public function testNamesCollectEachRepetition(): void
    {
        // PID.5 and PID.6 are repeating names; the family name is a nested component.
        $names = $this->pid->getPatientName();
        $this->assertCount(2, $names);
        $this->assertSame('DUCK', $names[0]->familyName->surname->getValue());
        $this->assertSame('DONALD', $names[0]->givenName->getValue());
        $this->assertSame('MOUSE', $names[1]->familyName->surname->getValue());

        $maiden = $this->pid->getMotherMaidenName();
        $this->assertCount(1, $maiden);
        $this->assertSame('DUCK', $maiden[0]->familyName->surname->getValue());
    }

    public function testDateFieldsMapToTheirValues(): void
    {
        $this->assertSame('19241010', $this->pid->getDateOfBirth()->getValue());
        $this->assertSame('20200101', $this->pid->getPatientDeathDateAndTime()->getValue());
        $this->assertSame('20210101120000', $this->pid->getLastUpdateDateTime()->getValue());
    }

    public function testCodedFieldsMapToTheirLeadingIdentifier(): void
    {
        $this->assertSame('M', $this->pid->getAdministrativeSex()->identifier->getValue());
        $this->assertSame('EN', $this->pid->getPrimaryLanguage()->identifier->getValue());
        $this->assertSame('M', $this->pid->getMaritalStatus()->identifier->getValue());
        $this->assertSame('CHR', $this->pid->getReligion()->identifier->getValue());
        $this->assertSame('V', $this->pid->getVeteransMilitaryStatus()->identifier->getValue());
        $this->assertSame('US', $this->pid->getNationality()->identifier->getValue());
        $this->assertSame('TAX', $this->pid->getTaxonomicClassificationCode()->identifier->getValue());
        $this->assertSame('BREED', $this->pid->getBreedCode()->identifier->getValue());
        $this->assertSame('PROD', $this->pid->getProductionClassCode()->identifier->getValue());
    }

    public function testRepeatingCodedFieldsCollectEachEntry(): void
    {
        $race = $this->pid->getRace();
        $this->assertCount(2, $race);
        $this->assertSame('2106-3', $race[0]->identifier->getValue());
        $this->assertSame('1002-5', $race[1]->identifier->getValue());

        $this->assertSame('H', $this->pid->getEthnicGroup()[0]->identifier->getValue());
        $this->assertSame('USA', $this->pid->getCitizenship()[0]->identifier->getValue());
        $this->assertSame('OK', $this->pid->getIdentityReliabilityCode()[0]->identifier->getValue());
        $this->assertSame('TRIBE', $this->pid->getTribalCitizenship()[0]->identifier->getValue());
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
        $this->assertSame('111 DUCK ST', $addresses[0]->streetAddress->streetAddress->getValue());
        $this->assertSame('FOWL', $addresses[0]->city->getValue());
        $this->assertSame('222 GOOSE LN', $addresses[1]->streetAddress->streetAddress->getValue());
    }

    public function testPhoneNumbersCollectEachRepetition(): void
    {
        $home = $this->pid->getPhoneNumberHome();
        $this->assertCount(2, $home);
        $this->assertSame('8885551212', $home[0]->telephoneNumber->getValue());
        $this->assertSame('8885551213', $home[1]->telephoneNumber->getValue());

        $this->assertSame('8885551214', $this->pid->getPhoneNumberBusiness()[0]->telephoneNumber->getValue());
        $this->assertSame(
            '8885551215',
            $this->pid->getPatientTelecommunicationInformation()[0]->telephoneNumber->getValue(),
        );
    }

    public function testAccountNumberMapsToItsComponents(): void
    {
        $account = $this->pid->getAccountNumber();
        $this->assertSame('40007716', $account->id->getValue());
        $this->assertSame('VN', $account->identifierTypeCode->getValue());
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
        $this->assertSame('FACILITY', $this->pid->getLastUpdateFacility()->namespaceId->getValue());
        $this->assertSame('ISO', $this->pid->getLastUpdateFacility()->universalIdType->getValue());
    }
}
