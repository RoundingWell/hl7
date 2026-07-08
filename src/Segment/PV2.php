<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\DT;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\PL;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XCN;
use RoundingWell\HL7\DataType\XON;
use RoundingWell\HL7\Field;
use RoundingWell\HL7\Segment;

/**
 * Patient Visit - Additional Information Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class PV2 extends Segment
{
    /**
     * @mago-expect lint:halstead
     */
    public function __construct()
    {
        parent::__construct('PV2');

        $this->addField(1, new Field('Prior Pending Location', PL::class));
        $this->addField(2, new Field('Accommodation Code', CWE::class));
        $this->addField(3, new Field('Admit Reason', CWE::class));
        $this->addField(4, new Field('Transfer Reason', CWE::class));
        $this->addField(5, new Field('Patient Valuables', ST::class, repeating: true));
        $this->addField(6, new Field('Patient Valuables Location', ST::class));
        $this->addField(7, new Field('Visit User Code', CWE::class, repeating: true));
        $this->addField(8, new Field('Expected Admit Date/Time', DTM::class));
        $this->addField(9, new Field('Expected Discharge Date/Time', DTM::class));
        $this->addField(10, new Field('Estimated Length of Inpatient Stay', NM::class));
        $this->addField(11, new Field('Actual Length of Inpatient Stay', NM::class));
        $this->addField(12, new Field('Visit Description', ST::class));
        $this->addField(13, new Field('Referral Source Code', XCN::class, repeating: true));
        $this->addField(14, new Field('Previous Service Date', DT::class));
        $this->addField(15, new Field('Employment Illness Related Indicator', ID::class, args: ['table' => 136]));
        $this->addField(16, new Field('Purge Status Code', CWE::class));
        $this->addField(17, new Field('Purge Status Date', DT::class));
        $this->addField(18, new Field('Special Program Code', CWE::class));
        $this->addField(19, new Field('Retention Indicator', ID::class, args: ['table' => 136]));
        $this->addField(20, new Field('Expected Number of Insurance Plans', NM::class));
        $this->addField(21, new Field('Visit Publicity Code', CWE::class));
        $this->addField(22, new Field('Visit Protection Indicator', ID::class, args: ['table' => 136]));
        $this->addField(23, new Field('Clinic Organization Name', XON::class, repeating: true));
        $this->addField(24, new Field('Patient Status Code', CWE::class));
        $this->addField(25, new Field('Visit Priority Code', CWE::class));
        $this->addField(26, new Field('Previous Treatment Date', DT::class));
        $this->addField(27, new Field('Expected Discharge Disposition', CWE::class));
        $this->addField(28, new Field('Signature on File Date', DT::class));
        $this->addField(29, new Field('First Similar Illness Date', DT::class));
        $this->addField(30, new Field('Patient Charge Adjustment Code', CWE::class));
        $this->addField(31, new Field('Recurring Service Code', CWE::class));
        $this->addField(32, new Field('Billing Media Code', ID::class, args: ['table' => 136]));
        $this->addField(33, new Field('Expected Surgery Date and Time', DTM::class));
        $this->addField(34, new Field('Military Partnership Code', ID::class, args: ['table' => 136]));
        $this->addField(35, new Field('Military Non-Availability Code', ID::class, args: ['table' => 136]));
        $this->addField(36, new Field('Newborn Baby Indicator', ID::class, args: ['table' => 136]));
        $this->addField(37, new Field('Baby Detained Indicator', ID::class, args: ['table' => 136]));
        $this->addField(38, new Field('Mode of Arrival Code', CWE::class));
        $this->addField(39, new Field('Recreational Drug Use Code', CWE::class, repeating: true));
        $this->addField(40, new Field('Admission Level of Care Code', CWE::class));
        $this->addField(41, new Field('Precaution Code', CWE::class, repeating: true));
        $this->addField(42, new Field('Patient Condition Code', CWE::class));
        $this->addField(43, new Field('Living Will Code', CWE::class));
        $this->addField(44, new Field('Organ Donor Code', CWE::class));
        $this->addField(45, new Field('Advance Directive Code', CWE::class, repeating: true));
        $this->addField(46, new Field('Patient Status Effective Date', DT::class));
        $this->addField(47, new Field('Expected LOA Return Date/Time', DTM::class));
        $this->addField(48, new Field('Expected Pre-admission Testing Date/Time', DTM::class));
        $this->addField(49, new Field('Notify Clergy Code', CWE::class, repeating: true));
        $this->addField(50, new Field('Advance Directive Last Verified Date', DT::class));
    }

    public function getPriorPendingLocation(): PL
    {
        return $this->getField(1)->getInstance();
    }

    public function getAccommodationCode(): CWE
    {
        return $this->getField(2)->getInstance();
    }

    public function getAdmitReason(): CWE
    {
        return $this->getField(3)->getInstance();
    }

    public function getTransferReason(): CWE
    {
        return $this->getField(4)->getInstance();
    }

    /**
     * @return list<ST>
     */
    public function getPatientValuables(): array
    {
        return $this->getField(5)->getInstance();
    }

    public function getPatientValuablesLocation(): ST
    {
        return $this->getField(6)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getVisitUserCode(): array
    {
        return $this->getField(7)->getInstance();
    }

    public function getExpectedAdmitDateTime(): DTM
    {
        return $this->getField(8)->getInstance();
    }

    public function getExpectedDischargeDateTime(): DTM
    {
        return $this->getField(9)->getInstance();
    }

    public function getEstimatedInpatientStayLength(): NM
    {
        return $this->getField(10)->getInstance();
    }

    public function getActualInpatientStayLength(): NM
    {
        return $this->getField(11)->getInstance();
    }

    public function getVisitDescription(): ST
    {
        return $this->getField(12)->getInstance();
    }

    /**
     * @return list<XCN>
     */
    public function getReferralSourceCode(): array
    {
        return $this->getField(13)->getInstance();
    }

    public function getPreviousServiceDate(): DT
    {
        return $this->getField(14)->getInstance();
    }

    public function getEmploymentIllnessRelatedIndicator(): ID
    {
        return $this->getField(15)->getInstance();
    }

    public function getPurgeStatusCode(): CWE
    {
        return $this->getField(16)->getInstance();
    }

    public function getPurgeStatusDate(): DT
    {
        return $this->getField(17)->getInstance();
    }

    public function getSpecialProgramCode(): CWE
    {
        return $this->getField(18)->getInstance();
    }

    public function getRetentionIndicator(): ID
    {
        return $this->getField(19)->getInstance();
    }

    public function getExpectedInsurancePlanCount(): NM
    {
        return $this->getField(20)->getInstance();
    }

    public function getVisitPublicityCode(): CWE
    {
        return $this->getField(21)->getInstance();
    }

    public function getVisitProtectionIndicator(): ID
    {
        return $this->getField(22)->getInstance();
    }

    /**
     * @return list<XON>
     */
    public function getClinicOrganizationName(): array
    {
        return $this->getField(23)->getInstance();
    }

    public function getPatientStatusCode(): CWE
    {
        return $this->getField(24)->getInstance();
    }

    public function getVisitPriorityCode(): CWE
    {
        return $this->getField(25)->getInstance();
    }

    public function getPreviousTreatmentDate(): DT
    {
        return $this->getField(26)->getInstance();
    }

    public function getExpectedDischargeDisposition(): CWE
    {
        return $this->getField(27)->getInstance();
    }

    public function getSignatureOnFileDate(): DT
    {
        return $this->getField(28)->getInstance();
    }

    public function getFirstSimilarIllnessDate(): DT
    {
        return $this->getField(29)->getInstance();
    }

    public function getPatientChargeAdjustmentCode(): CWE
    {
        return $this->getField(30)->getInstance();
    }

    public function getRecurringServiceCode(): CWE
    {
        return $this->getField(31)->getInstance();
    }

    public function getBillingMediaCode(): ID
    {
        return $this->getField(32)->getInstance();
    }

    public function getExpectedSurgeryDateTime(): DTM
    {
        return $this->getField(33)->getInstance();
    }

    public function getMilitaryPartnershipCode(): ID
    {
        return $this->getField(34)->getInstance();
    }

    public function getMilitaryNonAvailabilityCode(): ID
    {
        return $this->getField(35)->getInstance();
    }

    public function getNewbornBabyIndicator(): ID
    {
        return $this->getField(36)->getInstance();
    }

    public function getBabyDetainedIndicator(): ID
    {
        return $this->getField(37)->getInstance();
    }

    public function getModeOfArrivalCode(): CWE
    {
        return $this->getField(38)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getRecreationalDrugUseCode(): array
    {
        return $this->getField(39)->getInstance();
    }

    public function getAdmissionLevelOfCareCode(): CWE
    {
        return $this->getField(40)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getPrecautionCode(): array
    {
        return $this->getField(41)->getInstance();
    }

    public function getPatientConditionCode(): CWE
    {
        return $this->getField(42)->getInstance();
    }

    public function getLivingWillCode(): CWE
    {
        return $this->getField(43)->getInstance();
    }

    public function getOrganDonorCode(): CWE
    {
        return $this->getField(44)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getAdvanceDirectiveCode(): array
    {
        return $this->getField(45)->getInstance();
    }

    public function getPatientStatusEffectiveDate(): DT
    {
        return $this->getField(46)->getInstance();
    }

    public function getExpectedLoaReturnDateTime(): DTM
    {
        return $this->getField(47)->getInstance();
    }

    public function getExpectedPreAdmissionTestingDateTime(): DTM
    {
        return $this->getField(48)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getNotifyClergyCode(): array
    {
        return $this->getField(49)->getInstance();
    }

    public function getAdvanceDirectiveLastVerifiedDate(): DT
    {
        return $this->getField(50)->getInstance();
    }
}
