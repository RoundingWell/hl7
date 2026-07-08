<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\PV2;

#[CoversClass(PV2::class)]
final class PV2Test extends TestCase
{
    private PV2 $pv2;

    #[Override]
    protected function setUp(): void
    {
        $this->pv2 = new PV2();
        $this->pv2->setRaw(new Encoding(), [
            'WEST^101^A', // PV2.1 Prior Pending Location
            'PVT^Private', // PV2.2 Accommodation Code
            'FEVER^Fever', // PV2.3 Admit Reason
            'BED^Bed change', // PV2.4 Transfer Reason
            'Watch~Ring', // PV2.5 Patient Valuables (repeating)
            'Safe', // PV2.6 Patient Valuables Location
            'U1^User one~U2^User two', // PV2.7 Visit User Code (repeating)
            '20050110', // PV2.8 Expected Admit Date/Time
            '20050115', // PV2.9 Expected Discharge Date/Time
            '5', // PV2.10 Estimated Length of Inpatient Stay
            '4', // PV2.11 Actual Length of Inpatient Stay
            'Routine visit', // PV2.12 Visit Description
            '37^DISNEY~38^MOUSE', // PV2.13 Referral Source Code (repeating)
            '20041231', // PV2.14 Previous Service Date
            'Y', // PV2.15 Employment Illness Related Indicator
            'A^Active', // PV2.16 Purge Status Code
            '20050201', // PV2.17 Purge Status Date
            'SP^Special', // PV2.18 Special Program Code
            'Y', // PV2.19 Retention Indicator
            '2', // PV2.20 Expected Number of Insurance Plans
            'F^Family', // PV2.21 Visit Publicity Code
            'N', // PV2.22 Visit Protection Indicator
            'Clinic A~Clinic B', // PV2.23 Clinic Organization Name (repeating)
            'AC^Active', // PV2.24 Patient Status Code
            'P1^Priority one', // PV2.25 Visit Priority Code
            '20040101', // PV2.26 Previous Treatment Date
            'HOME^Home', // PV2.27 Expected Discharge Disposition
            '20050109', // PV2.28 Signature on File Date
            '20030601', // PV2.29 First Similar Illness Date
            'ADJ^Adjustment', // PV2.30 Patient Charge Adjustment Code
            'RS^Recurring', // PV2.31 Recurring Service Code
            'N', // PV2.32 Billing Media Code
            '20050112', // PV2.33 Expected Surgery Date and Time
            'N', // PV2.34 Military Partnership Code
            'N', // PV2.35 Military Non-Availability Code
            'N', // PV2.36 Newborn Baby Indicator
            'N', // PV2.37 Baby Detained Indicator
            'AMB^Ambulance', // PV2.38 Mode of Arrival Code
            'NONE^None~ALC^Alcohol', // PV2.39 Recreational Drug Use Code (repeating)
            'LOC^Level of care', // PV2.40 Admission Level of Care Code
            'ISO^Isolation~FALL^Fall', // PV2.41 Precaution Code (repeating)
            'STA^Stable', // PV2.42 Patient Condition Code
            'Y^Yes', // PV2.43 Living Will Code
            'D^Donor', // PV2.44 Organ Donor Code
            'DNR^Do not resuscitate~AD^Advance', // PV2.45 Advance Directive Code (repeating)
            '20050110', // PV2.46 Patient Status Effective Date
            '20050113', // PV2.47 Expected LOA Return Date/Time
            '20050108', // PV2.48 Expected Pre-admission Testing Date/Time
            'CATH^Catholic~PROT^Protestant', // PV2.49 Notify Clergy Code (repeating)
            '20050107', // PV2.50 Advance Directive Last Verified Date
        ]);
    }

    public function testLocationFieldsMapToTheirLeadingComponents(): void
    {
        // PV2.1 is a person location; its point of care leads the location hierarchy.
        $this->assertSame('WEST', $this->pv2->getPriorPendingLocation()->pointOfCare->namespaceId->getValue());
        $this->assertSame('Safe', $this->pv2->getPatientValuablesLocation()->getValue());
        $this->assertSame('Routine visit', $this->pv2->getVisitDescription()->getValue());
    }

    public function testCodedFieldsMapToTheirLeadingIdentifier(): void
    {
        $this->assertSame('PVT', $this->pv2->getAccommodationCode()->identifier->getValue());
        $this->assertSame('FEVER', $this->pv2->getAdmitReason()->identifier->getValue());
        $this->assertSame('BED', $this->pv2->getTransferReason()->identifier->getValue());
        $this->assertSame('A', $this->pv2->getPurgeStatusCode()->identifier->getValue());
        $this->assertSame('SP', $this->pv2->getSpecialProgramCode()->identifier->getValue());
        $this->assertSame('F', $this->pv2->getVisitPublicityCode()->identifier->getValue());
        $this->assertSame('AC', $this->pv2->getPatientStatusCode()->identifier->getValue());
        $this->assertSame('P1', $this->pv2->getVisitPriorityCode()->identifier->getValue());
        $this->assertSame('HOME', $this->pv2->getExpectedDischargeDisposition()->identifier->getValue());
        $this->assertSame('ADJ', $this->pv2->getPatientChargeAdjustmentCode()->identifier->getValue());
        $this->assertSame('RS', $this->pv2->getRecurringServiceCode()->identifier->getValue());
        $this->assertSame('AMB', $this->pv2->getModeOfArrivalCode()->identifier->getValue());
        $this->assertSame('LOC', $this->pv2->getAdmissionLevelOfCareCode()->identifier->getValue());
        $this->assertSame('STA', $this->pv2->getPatientConditionCode()->identifier->getValue());
        $this->assertSame('Y', $this->pv2->getLivingWillCode()->identifier->getValue());
        $this->assertSame('D', $this->pv2->getOrganDonorCode()->identifier->getValue());
    }

    public function testDateAndDateTimeFieldsMapToTheirValues(): void
    {
        $this->assertSame('20050110', $this->pv2->getExpectedAdmitDateTime()->getValue());
        $this->assertSame('20050115', $this->pv2->getExpectedDischargeDateTime()->getValue());
        $this->assertSame('20041231', $this->pv2->getPreviousServiceDate()->getValue());
        $this->assertSame('20050201', $this->pv2->getPurgeStatusDate()->getValue());
        $this->assertSame('20040101', $this->pv2->getPreviousTreatmentDate()->getValue());
        $this->assertSame('20050109', $this->pv2->getSignatureOnFileDate()->getValue());
        $this->assertSame('20030601', $this->pv2->getFirstSimilarIllnessDate()->getValue());
        $this->assertSame('20050112', $this->pv2->getExpectedSurgeryDateTime()->getValue());
        $this->assertSame('20050110', $this->pv2->getPatientStatusEffectiveDate()->getValue());
        $this->assertSame('20050113', $this->pv2->getExpectedLoaReturnDateTime()->getValue());
        $this->assertSame('20050108', $this->pv2->getExpectedPreAdmissionTestingDateTime()->getValue());
        $this->assertSame('20050107', $this->pv2->getAdvanceDirectiveLastVerifiedDate()->getValue());
    }

    public function testNumericFieldsMapToTheirValues(): void
    {
        $this->assertSame('5', $this->pv2->getEstimatedInpatientStayLength()->getValue());
        $this->assertSame('4', $this->pv2->getActualInpatientStayLength()->getValue());
        $this->assertSame('2', $this->pv2->getExpectedInsurancePlanCount()->getValue());
    }

    public function testIndicatorFieldsMapToTheirValues(): void
    {
        // Table 136 indicators carry a coded yes/no answer for each visit attribute.
        $this->assertSame('Y', $this->pv2->getEmploymentIllnessRelatedIndicator()->getValue());
        $this->assertSame('Y', $this->pv2->getRetentionIndicator()->getValue());
        $this->assertSame('N', $this->pv2->getVisitProtectionIndicator()->getValue());
        $this->assertSame('N', $this->pv2->getBillingMediaCode()->getValue());
        $this->assertSame('N', $this->pv2->getMilitaryPartnershipCode()->getValue());
        $this->assertSame('N', $this->pv2->getMilitaryNonAvailabilityCode()->getValue());
        $this->assertSame('N', $this->pv2->getNewbornBabyIndicator()->getValue());
        $this->assertSame('N', $this->pv2->getBabyDetainedIndicator()->getValue());
    }

    public function testRepeatingScalarFieldsCollectEachEntry(): void
    {
        // PV2.5 is repeating, so every valuable must be retained in order.
        $valuables = $this->pv2->getPatientValuables();
        $this->assertCount(2, $valuables);
        $this->assertSame('Watch', $valuables[0]->getValue());
        $this->assertSame('Ring', $valuables[1]->getValue());
    }

    public function testRepeatingCodedFieldsCollectEachEntry(): void
    {
        $userCodes = $this->pv2->getVisitUserCode();
        $this->assertCount(2, $userCodes);
        $this->assertSame('U1', $userCodes[0]->identifier->getValue());
        $this->assertSame('U2', $userCodes[1]->identifier->getValue());

        $this->assertSame('NONE', $this->pv2->getRecreationalDrugUseCode()[0]->identifier->getValue());
        $this->assertSame('ISO', $this->pv2->getPrecautionCode()[0]->identifier->getValue());
        $this->assertSame('DNR', $this->pv2->getAdvanceDirectiveCode()[0]->identifier->getValue());
        $this->assertSame('CATH', $this->pv2->getNotifyClergyCode()[0]->identifier->getValue());
    }

    public function testRepeatingReferralAndClinicFieldsCollectEachEntry(): void
    {
        // PV2.13 references people; PV2.23 references organizations, both repeating.
        $referrals = $this->pv2->getReferralSourceCode();
        $this->assertCount(2, $referrals);
        $this->assertSame('37', $referrals[0]->id->getValue());
        $this->assertSame('DISNEY', $referrals[0]->familyName->surname->getValue());
        $this->assertSame('38', $referrals[1]->id->getValue());

        $clinics = $this->pv2->getClinicOrganizationName();
        $this->assertCount(2, $clinics);
        $this->assertSame('Clinic A', $clinics[0]->name->getValue());
        $this->assertSame('Clinic B', $clinics[1]->name->getValue());
    }
}
