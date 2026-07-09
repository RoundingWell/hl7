<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\PV1;

#[CoversClass(PV1::class)]
final class PV1Test extends TestCase
{
    private PV1 $pv1;

    #[Override]
    protected function setUp(): void
    {
        $this->pv1 = new PV1();
        $this->pv1->parse(new Encoding(), implode('|', [
            'PV1', // Segment name
            '1', // PV1.1 Set ID
            'I^Inpatient', // PV1.2 Patient Class
            'PtCare^Room1^Bed1^Facility', // PV1.3 Assigned Patient Location
            'A^Accident', // PV1.4 Admission Type
            'PRE123^^^AccMgr^MR', // PV1.5 Preadmit Number
            'PriorCare^Room0^Bed0', // PV1.6 Prior Patient Location
            '1000^ATTEND^ALAN~1001^ATTEND^ANNA', // PV1.7 Attending Doctor (repeating)
            '2000^REFER^ROBERT', // PV1.8 Referring Doctor (repeating)
            '3000^CONSULT^CARL', // PV1.9 Consulting Doctor (repeating)
            'MED^Medical', // PV1.10 Hospital Service
            'TempCare^RoomT^BedT', // PV1.11 Temporary Location
            'Y^Yes', // PV1.12 Preadmit Test Indicator
            'R^Readmit', // PV1.13 Re-admission Indicator
            'ER^Emergency', // PV1.14 Admit Source
            'A0^No restrictions~A1^Ambulates', // PV1.15 Ambulatory Status (repeating)
            'VIP^Very Important', // PV1.16 VIP Indicator
            '4000^ADMIT^AMY~4001^ADMIT^ADAM', // PV1.17 Admitting Doctor (repeating)
            'IP^Inpatient Type', // PV1.18 Patient Type
            'VN123^^^AccMgr^VN', // PV1.19 Visit Number
            'FC1^20200101~FC2^20200201', // PV1.20 Financial Class (repeating; code + effective date)
            'CPI^Charge', // PV1.21 Charge Price Indicator
            'CC^Courtesy', // PV1.22 Courtesy Code
            'CR^Good', // PV1.23 Credit Rating
            'CON1^Contract A~CON2^Contract B', // PV1.24 Contract Code (repeating)
            '20200101~20200201', // PV1.25 Contract Effective Date (repeating)
            '100~200', // PV1.26 Contract Amount (repeating)
            '12~24', // PV1.27 Contract Period (repeating)
            'INT^Interest', // PV1.28 Interest Code
            'TBD^Transfer', // PV1.29 Transfer to Bad Debt Code
            '20200301', // PV1.30 Transfer to Bad Debt Date
            'AGY^Agency', // PV1.31 Bad Debt Agency Code
            '500', // PV1.32 Bad Debt Transfer Amount
            '250', // PV1.33 Bad Debt Recovery Amount
            'DEL^Delete', // PV1.34 Delete Account Indicator
            '20200401', // PV1.35 Delete Account Date
            'HOME^Home', // PV1.36 Discharge Disposition
            'DISLOC^20200115', // PV1.37 Discharged to Location (code + effective date)
            'REG^Regular', // PV1.38 Diet Type
            'SF^Servicing', // PV1.39 Servicing Facility
            'BS^Occupied', // PV1.40 Bed Status
            'ACT^Active', // PV1.41 Account Status
            'PendCare^RoomP^BedP', // PV1.42 Pending Location
            'PriorTemp^RoomPT^BedPT', // PV1.43 Prior Temporary Location
            '20200110120000', // PV1.44 Admit Date/Time
            '20200115130000', // PV1.45 Discharge Date/Time
            '1000', // PV1.46 Current Patient Balance
            '5000', // PV1.47 Total Charges
            '100', // PV1.48 Total Adjustments
            '3900', // PV1.49 Total Payments
            'ALT1^^^AccMgr^VN~ALT2^^^AccMgr^VN', // PV1.50 Alternate Visit ID (repeating)
            'VI^Visit', // PV1.51 Visit Indicator
            'ProviderX', // PV1.52 Other Healthcare Provider
            'Episode description', // PV1.53 Service Episode Description
            'EPI123^^^AccMgr^SE', // PV1.54 Service Episode Identifier
        ]));
    }

    public function testSequenceAndClassMapToTheirValues(): void
    {
        $this->assertSame('1', $this->pv1->getIdentity()->getValue());
        // PV1.2 is required; the patient class drives downstream visit handling.
        $this->assertSame('I', $this->pv1->getPatientClass()->getIdentifier()->getValue());
        $this->assertSame('IP', $this->pv1->getPatientType()->getIdentifier()->getValue());
        $this->assertSame('VI', $this->pv1->getVisitIndicator()->getIdentifier()->getValue());
    }

    public function testLocationsMapToTheirLeadingPointOfCare(): void
    {
        // Each PL field exposes the point of care as its leading component.
        $this->assertSame(
            'PtCare',
            $this->pv1->getAssignedPatientLocation()->getPointOfCare()->getNamespaceId()->getValue(),
        );
        $this->assertSame(
            'PriorCare',
            $this->pv1->getPriorPatientLocation()->getPointOfCare()->getNamespaceId()->getValue(),
        );
        $this->assertSame(
            'TempCare',
            $this->pv1->getTemporaryLocation()->getPointOfCare()->getNamespaceId()->getValue(),
        );
        $this->assertSame('PendCare', $this->pv1->getPendingLocation()->getPointOfCare()->getNamespaceId()->getValue());
        $this->assertSame(
            'PriorTemp',
            $this->pv1->getPriorTemporaryLocation()->getPointOfCare()->getNamespaceId()->getValue(),
        );
    }

    public function testCodedFieldsMapToTheirLeadingIdentifier(): void
    {
        $this->assertSame('A', $this->pv1->getAdmissionType()->getIdentifier()->getValue());
        $this->assertSame('MED', $this->pv1->getHospitalService()->getIdentifier()->getValue());
        $this->assertSame('Y', $this->pv1->getPreadmitTestIndicator()->getIdentifier()->getValue());
        $this->assertSame('R', $this->pv1->getReadmissionIndicator()->getIdentifier()->getValue());
        $this->assertSame('ER', $this->pv1->getAdmitSource()->getIdentifier()->getValue());
        $this->assertSame('VIP', $this->pv1->getVipIndicator()->getIdentifier()->getValue());
        $this->assertSame('CPI', $this->pv1->getChargePriceIndicator()->getIdentifier()->getValue());
        $this->assertSame('CC', $this->pv1->getCourtesyCode()->getIdentifier()->getValue());
        $this->assertSame('CR', $this->pv1->getCreditRating()->getIdentifier()->getValue());
        $this->assertSame('INT', $this->pv1->getInterestCode()->getIdentifier()->getValue());
        $this->assertSame('TBD', $this->pv1->getTransferToBadDebtCode()->getIdentifier()->getValue());
        $this->assertSame('AGY', $this->pv1->getBadDebtAgencyCode()->getIdentifier()->getValue());
        $this->assertSame('DEL', $this->pv1->getDeleteAccountIndicator()->getIdentifier()->getValue());
        $this->assertSame('HOME', $this->pv1->getDischargeDisposition()->getIdentifier()->getValue());
        $this->assertSame('REG', $this->pv1->getDietType()->getIdentifier()->getValue());
        $this->assertSame('SF', $this->pv1->getServicingFacility()->getIdentifier()->getValue());
        $this->assertSame('BS', $this->pv1->getBedStatus()->getIdentifier()->getValue());
        $this->assertSame('ACT', $this->pv1->getAccountStatus()->getIdentifier()->getValue());
    }

    public function testDoctorFieldsCollectEachRepetition(): void
    {
        // Each doctor role is repeating; every referenced clinician must be retained in order.
        $attending = $this->pv1->getAttendingDoctor();
        $this->assertCount(2, $attending);
        $this->assertSame('1000', $attending[0]->getId()->getValue());
        $this->assertSame('ATTEND', $attending[0]->getFamilyName()->getSurname()->getValue());
        $this->assertSame('1001', $attending[1]->getId()->getValue());

        $this->assertSame('2000', $this->pv1->getReferringDoctor()[0]->getId()->getValue());
        $this->assertSame('3000', $this->pv1->getConsultingDoctor()[0]->getId()->getValue());

        $admitting = $this->pv1->getAdmittingDoctor();
        $this->assertCount(2, $admitting);
        $this->assertSame('4000', $admitting[0]->getId()->getValue());
        $this->assertSame('4001', $admitting[1]->getId()->getValue());
    }

    public function testIdentifierFieldsMapToTheirComponents(): void
    {
        $this->assertSame('PRE123', $this->pv1->getPreadmitNumber()->getId()->getValue());
        $this->assertSame('VN123', $this->pv1->getVisitNumber()->getId()->getValue());
        $this->assertSame('VN', $this->pv1->getVisitNumber()->getIdentifierTypeCode()->getValue());
        $this->assertSame('EPI123', $this->pv1->getServiceEpisodeIdentifier()->getId()->getValue());

        // PV1.50 is repeating; every alternate visit identifier must be retained.
        $alternates = $this->pv1->getAlternateVisitId();
        $this->assertCount(2, $alternates);
        $this->assertSame('ALT1', $alternates[0]->getId()->getValue());
        $this->assertSame('ALT2', $alternates[1]->getId()->getValue());
    }

    public function testAmbulatoryStatusCollectsEachRepetition(): void
    {
        $statuses = $this->pv1->getAmbulatoryStatus();
        $this->assertCount(2, $statuses);
        $this->assertSame('A0', $statuses[0]->getIdentifier()->getValue());
        $this->assertSame('A1', $statuses[1]->getIdentifier()->getValue());
    }

    public function testFinancialFieldsCollectEachRepetition(): void
    {
        // PV1.20 is repeating; the financial class code is a nested coded component.
        $financial = $this->pv1->getFinancialClass();
        $this->assertCount(2, $financial);
        $this->assertSame('FC1', $financial[0]->getFinancialClassCode()->getIdentifier()->getValue());
        $this->assertSame('20200101', $financial[0]->getEffectiveDate()->getValue());
        $this->assertSame('FC2', $financial[1]->getFinancialClassCode()->getIdentifier()->getValue());

        $contracts = $this->pv1->getContractCode();
        $this->assertCount(2, $contracts);
        $this->assertSame('CON1', $contracts[0]->getIdentifier()->getValue());
        $this->assertSame('CON2', $contracts[1]->getIdentifier()->getValue());

        $dates = $this->pv1->getContractEffectiveDate();
        $this->assertCount(2, $dates);
        $this->assertSame('20200101', $dates[0]->getValue());
        $this->assertSame('20200201', $dates[1]->getValue());

        $amounts = $this->pv1->getContractAmount();
        $this->assertCount(2, $amounts);
        $this->assertSame('100', $amounts[0]->getValue());
        $this->assertSame('200', $amounts[1]->getValue());

        $periods = $this->pv1->getContractPeriod();
        $this->assertCount(2, $periods);
        $this->assertSame('12', $periods[0]->getValue());
        $this->assertSame('24', $periods[1]->getValue());
    }

    public function testBadDebtAndBalanceFieldsMapToTheirValues(): void
    {
        $this->assertSame('20200301', $this->pv1->getTransferToBadDebtDate()->getValue());
        $this->assertSame('500', $this->pv1->getBadDebtTransferAmount()->getValue());
        $this->assertSame('250', $this->pv1->getBadDebtRecoveryAmount()->getValue());
        $this->assertSame('20200401', $this->pv1->getDeleteAccountDate()->getValue());
        $this->assertSame('1000', $this->pv1->getCurrentPatientBalance()->getValue());
        $this->assertSame('5000', $this->pv1->getTotalCharges()->getValue());
        $this->assertSame('100', $this->pv1->getTotalAdjustments()->getValue());
        $this->assertSame('3900', $this->pv1->getTotalPayments()->getValue());
    }

    public function testDischargeFieldsMapToTheirComponents(): void
    {
        // PV1.37 exposes the discharge location as its leading coded component.
        $this->assertSame(
            'DISLOC',
            $this->pv1->getDischargedToLocation()->getDischargeLocation()->getIdentifier()->getValue(),
        );
        $this->assertSame('20200115', $this->pv1->getDischargedToLocation()->getEffectiveDate()->getValue());
        $this->assertSame('20200110120000', $this->pv1->getAdmitDateTime()->getValue());
        $this->assertSame('20200115130000', $this->pv1->getDischargeDateTime()->getValue());
    }

    public function testFreeTextFieldsMapToTheirValues(): void
    {
        $this->assertSame('ProviderX', $this->pv1->getOtherHealthcareProvider()->getValue());
        $this->assertSame('Episode description', $this->pv1->getServiceEpisodeDescription()->getValue());
    }
}
