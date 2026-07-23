<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\DT;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\PL;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XCN;
use RoundingWell\HL7\DataType\XON;
use RoundingWell\HL7\TypeDefinition;

/**
 * Patient Visit - Additional Information Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class PV2 extends AbstractSegment
{
    public function __construct()
    {
        $this->add(new TypeDefinition('Prior Pending Location', PL::class, maxReps: 1));
        $this->add(new TypeDefinition('Accommodation Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Admit Reason', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Transfer Reason', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Patient Valuables', ST::class));
        $this->add(new TypeDefinition('Patient Valuables Location', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Visit User Code', CWE::class));
        $this->add(new TypeDefinition('Expected Admit Date/Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Expected Discharge Date/Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Estimated Length of Inpatient Stay', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Actual Length of Inpatient Stay', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Visit Description', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Referral Source Code', XCN::class));
        $this->add(new TypeDefinition('Previous Service Date', DT::class, maxReps: 1));
        $this->add(
            new TypeDefinition('Employment Illness Related Indicator', ID::class, args: ['table' => 136], maxReps: 1),
        );
        $this->add(new TypeDefinition('Purge Status Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Purge Status Date', DT::class, maxReps: 1));
        $this->add(new TypeDefinition('Special Program Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Retention Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Expected Number of Insurance Plans', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Visit Publicity Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Visit Protection Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Clinic Organization Name', XON::class));
        $this->add(new TypeDefinition('Patient Status Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Visit Priority Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Previous Treatment Date', DT::class, maxReps: 1));
        $this->add(new TypeDefinition('Expected Discharge Disposition', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Signature on File Date', DT::class, maxReps: 1));
        $this->add(new TypeDefinition('First Similar Illness Date', DT::class, maxReps: 1));
        $this->add(new TypeDefinition('Patient Charge Adjustment Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Recurring Service Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Billing Media Code', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Expected Surgery Date and Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Military Partnership Code', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Military Non-Availability Code', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Newborn Baby Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Baby Detained Indicator', ID::class, args: ['table' => 136], maxReps: 1));
        $this->add(new TypeDefinition('Mode of Arrival Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Recreational Drug Use Code', CWE::class));
        $this->add(new TypeDefinition('Admission Level of Care Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Precaution Code', CWE::class));
        $this->add(new TypeDefinition('Patient Condition Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Living Will Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Organ Donor Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Advance Directive Code', CWE::class));
        $this->add(new TypeDefinition('Patient Status Effective Date', DT::class, maxReps: 1));
        $this->add(new TypeDefinition('Expected LOA Return Date/Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Expected Pre-admission Testing Date/Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Notify Clergy Code', CWE::class));
        $this->add(new TypeDefinition('Advance Directive Last Verified Date', DT::class, maxReps: 1));
    }

    /**
     * PV2.1 Prior Pending Location
     */
    public function getPriorPendingLocation(): PL
    {
        return $this->getFieldRepetition(1, 0);
    }

    /**
     * PV2.2 Accommodation Code
     */
    public function getAccommodationCode(): CWE
    {
        return $this->getFieldRepetition(2, 0);
    }

    /**
     * PV2.3 Admit Reason
     */
    public function getAdmitReason(): CWE
    {
        return $this->getFieldRepetition(3, 0);
    }

    /**
     * PV2.4 Transfer Reason
     */
    public function getTransferReason(): CWE
    {
        return $this->getFieldRepetition(4, 0);
    }

    /**
     * PV2.5 Patient Valuables
     *
     * @return list<ST>
     */
    public function getPatientValuables(): array
    {
        return $this->getField(5);
    }

    /**
     * PV2.6 Patient Valuables Location
     */
    public function getPatientValuablesLocation(): ST
    {
        return $this->getFieldRepetition(6, 0);
    }

    /**
     * PV2.7 Visit User Code
     *
     * @return list<CWE>
     */
    public function getVisitUserCode(): array
    {
        return $this->getField(7);
    }

    /**
     * PV2.8 Expected Admit Date/Time
     */
    public function getExpectedAdmitDateTime(): DTM
    {
        return $this->getFieldRepetition(8, 0);
    }

    /**
     * PV2.9 Expected Discharge Date/Time
     */
    public function getExpectedDischargeDateTime(): DTM
    {
        return $this->getFieldRepetition(9, 0);
    }

    /**
     * PV2.10 Estimated Length of Inpatient Stay
     */
    public function getEstimatedInpatientStayLength(): NM
    {
        return $this->getFieldRepetition(10, 0);
    }

    /**
     * PV2.11 Actual Length of Inpatient Stay
     */
    public function getActualInpatientStayLength(): NM
    {
        return $this->getFieldRepetition(11, 0);
    }

    /**
     * PV2.12 Visit Description
     */
    public function getVisitDescription(): ST
    {
        return $this->getFieldRepetition(12, 0);
    }

    /**
     * PV2.13 Referral Source Code
     *
     * @return list<XCN>
     */
    public function getReferralSourceCode(): array
    {
        return $this->getField(13);
    }

    /**
     * PV2.14 Previous Service Date
     */
    public function getPreviousServiceDate(): DT
    {
        return $this->getFieldRepetition(14, 0);
    }

    /**
     * PV2.15 Employment Illness Related Indicator
     */
    public function getEmploymentIllnessRelatedIndicator(): ID
    {
        return $this->getFieldRepetition(15, 0);
    }

    /**
     * PV2.16 Purge Status Code
     */
    public function getPurgeStatusCode(): CWE
    {
        return $this->getFieldRepetition(16, 0);
    }

    /**
     * PV2.17 Purge Status Date
     */
    public function getPurgeStatusDate(): DT
    {
        return $this->getFieldRepetition(17, 0);
    }

    /**
     * PV2.18 Special Program Code
     */
    public function getSpecialProgramCode(): CWE
    {
        return $this->getFieldRepetition(18, 0);
    }

    /**
     * PV2.19 Retention Indicator
     */
    public function getRetentionIndicator(): ID
    {
        return $this->getFieldRepetition(19, 0);
    }

    /**
     * PV2.20 Expected Number of Insurance Plans
     */
    public function getExpectedInsurancePlanCount(): NM
    {
        return $this->getFieldRepetition(20, 0);
    }

    /**
     * PV2.21 Visit Publicity Code
     */
    public function getVisitPublicityCode(): CWE
    {
        return $this->getFieldRepetition(21, 0);
    }

    /**
     * PV2.22 Visit Protection Indicator
     */
    public function getVisitProtectionIndicator(): ID
    {
        return $this->getFieldRepetition(22, 0);
    }

    /**
     * PV2.23 Clinic Organization Name
     *
     * @return list<XON>
     */
    public function getClinicOrganizationName(): array
    {
        return $this->getField(23);
    }

    /**
     * PV2.24 Patient Status Code
     */
    public function getPatientStatusCode(): CWE
    {
        return $this->getFieldRepetition(24, 0);
    }

    /**
     * PV2.25 Visit Priority Code
     */
    public function getVisitPriorityCode(): CWE
    {
        return $this->getFieldRepetition(25, 0);
    }

    /**
     * PV2.26 Previous Treatment Date
     */
    public function getPreviousTreatmentDate(): DT
    {
        return $this->getFieldRepetition(26, 0);
    }

    /**
     * PV2.27 Expected Discharge Disposition
     */
    public function getExpectedDischargeDisposition(): CWE
    {
        return $this->getFieldRepetition(27, 0);
    }

    /**
     * PV2.28 Signature on File Date
     */
    public function getSignatureOnFileDate(): DT
    {
        return $this->getFieldRepetition(28, 0);
    }

    /**
     * PV2.29 First Similar Illness Date
     */
    public function getFirstSimilarIllnessDate(): DT
    {
        return $this->getFieldRepetition(29, 0);
    }

    /**
     * PV2.30 Patient Charge Adjustment Code
     */
    public function getPatientChargeAdjustmentCode(): CWE
    {
        return $this->getFieldRepetition(30, 0);
    }

    /**
     * PV2.31 Recurring Service Code
     */
    public function getRecurringServiceCode(): CWE
    {
        return $this->getFieldRepetition(31, 0);
    }

    /**
     * PV2.32 Billing Media Code
     */
    public function getBillingMediaCode(): ID
    {
        return $this->getFieldRepetition(32, 0);
    }

    /**
     * PV2.33 Expected Surgery Date and Time
     */
    public function getExpectedSurgeryDateTime(): DTM
    {
        return $this->getFieldRepetition(33, 0);
    }

    /**
     * PV2.34 Military Partnership Code
     */
    public function getMilitaryPartnershipCode(): ID
    {
        return $this->getFieldRepetition(34, 0);
    }

    /**
     * PV2.35 Military Non-Availability Code
     */
    public function getMilitaryNonAvailabilityCode(): ID
    {
        return $this->getFieldRepetition(35, 0);
    }

    /**
     * PV2.36 Newborn Baby Indicator
     */
    public function getNewbornBabyIndicator(): ID
    {
        return $this->getFieldRepetition(36, 0);
    }

    /**
     * PV2.37 Baby Detained Indicator
     */
    public function getBabyDetainedIndicator(): ID
    {
        return $this->getFieldRepetition(37, 0);
    }

    /**
     * PV2.38 Mode of Arrival Code
     */
    public function getModeOfArrivalCode(): CWE
    {
        return $this->getFieldRepetition(38, 0);
    }

    /**
     * PV2.39 Recreational Drug Use Code
     *
     * @return list<CWE>
     */
    public function getRecreationalDrugUseCode(): array
    {
        return $this->getField(39);
    }

    /**
     * PV2.40 Admission Level of Care Code
     */
    public function getAdmissionLevelOfCareCode(): CWE
    {
        return $this->getFieldRepetition(40, 0);
    }

    /**
     * PV2.41 Precaution Code
     *
     * @return list<CWE>
     */
    public function getPrecautionCode(): array
    {
        return $this->getField(41);
    }

    /**
     * PV2.42 Patient Condition Code
     */
    public function getPatientConditionCode(): CWE
    {
        return $this->getFieldRepetition(42, 0);
    }

    /**
     * PV2.43 Living Will Code
     */
    public function getLivingWillCode(): CWE
    {
        return $this->getFieldRepetition(43, 0);
    }

    /**
     * PV2.44 Organ Donor Code
     */
    public function getOrganDonorCode(): CWE
    {
        return $this->getFieldRepetition(44, 0);
    }

    /**
     * PV2.45 Advance Directive Code
     *
     * @return list<CWE>
     */
    public function getAdvanceDirectiveCode(): array
    {
        return $this->getField(45);
    }

    /**
     * PV2.46 Patient Status Effective Date
     */
    public function getPatientStatusEffectiveDate(): DT
    {
        return $this->getFieldRepetition(46, 0);
    }

    /**
     * PV2.47 Expected LOA Return Date/Time
     */
    public function getExpectedLoaReturnDateTime(): DTM
    {
        return $this->getFieldRepetition(47, 0);
    }

    /**
     * PV2.48 Expected Pre-admission Testing Date/Time
     */
    public function getExpectedPreAdmissionTestingDateTime(): DTM
    {
        return $this->getFieldRepetition(48, 0);
    }

    /**
     * PV2.49 Notify Clergy Code
     *
     * @return list<CWE>
     */
    public function getNotifyClergyCode(): array
    {
        return $this->getField(49);
    }

    /**
     * PV2.50 Advance Directive Last Verified Date
     */
    public function getAdvanceDirectiveLastVerifiedDate(): DT
    {
        return $this->getFieldRepetition(50, 0);
    }
}
