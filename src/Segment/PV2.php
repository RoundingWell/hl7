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

    public function getPriorPendingLocation(): PL
    {
        return $this->getFieldRepetition(1, 0);
    }

    public function getAccommodationCode(): CWE
    {
        return $this->getFieldRepetition(2, 0);
    }

    public function getAdmitReason(): CWE
    {
        return $this->getFieldRepetition(3, 0);
    }

    public function getTransferReason(): CWE
    {
        return $this->getFieldRepetition(4, 0);
    }

    /**
     * @return list<ST>
     */
    public function getPatientValuables(): array
    {
        return $this->getField(5);
    }

    public function getPatientValuablesLocation(): ST
    {
        return $this->getFieldRepetition(6, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getVisitUserCode(): array
    {
        return $this->getField(7);
    }

    public function getExpectedAdmitDateTime(): DTM
    {
        return $this->getFieldRepetition(8, 0);
    }

    public function getExpectedDischargeDateTime(): DTM
    {
        return $this->getFieldRepetition(9, 0);
    }

    public function getEstimatedInpatientStayLength(): NM
    {
        return $this->getFieldRepetition(10, 0);
    }

    public function getActualInpatientStayLength(): NM
    {
        return $this->getFieldRepetition(11, 0);
    }

    public function getVisitDescription(): ST
    {
        return $this->getFieldRepetition(12, 0);
    }

    /**
     * @return list<XCN>
     */
    public function getReferralSourceCode(): array
    {
        return $this->getField(13);
    }

    public function getPreviousServiceDate(): DT
    {
        return $this->getFieldRepetition(14, 0);
    }

    public function getEmploymentIllnessRelatedIndicator(): ID
    {
        return $this->getFieldRepetition(15, 0);
    }

    public function getPurgeStatusCode(): CWE
    {
        return $this->getFieldRepetition(16, 0);
    }

    public function getPurgeStatusDate(): DT
    {
        return $this->getFieldRepetition(17, 0);
    }

    public function getSpecialProgramCode(): CWE
    {
        return $this->getFieldRepetition(18, 0);
    }

    public function getRetentionIndicator(): ID
    {
        return $this->getFieldRepetition(19, 0);
    }

    public function getExpectedInsurancePlanCount(): NM
    {
        return $this->getFieldRepetition(20, 0);
    }

    public function getVisitPublicityCode(): CWE
    {
        return $this->getFieldRepetition(21, 0);
    }

    public function getVisitProtectionIndicator(): ID
    {
        return $this->getFieldRepetition(22, 0);
    }

    /**
     * @return list<XON>
     */
    public function getClinicOrganizationName(): array
    {
        return $this->getField(23);
    }

    public function getPatientStatusCode(): CWE
    {
        return $this->getFieldRepetition(24, 0);
    }

    public function getVisitPriorityCode(): CWE
    {
        return $this->getFieldRepetition(25, 0);
    }

    public function getPreviousTreatmentDate(): DT
    {
        return $this->getFieldRepetition(26, 0);
    }

    public function getExpectedDischargeDisposition(): CWE
    {
        return $this->getFieldRepetition(27, 0);
    }

    public function getSignatureOnFileDate(): DT
    {
        return $this->getFieldRepetition(28, 0);
    }

    public function getFirstSimilarIllnessDate(): DT
    {
        return $this->getFieldRepetition(29, 0);
    }

    public function getPatientChargeAdjustmentCode(): CWE
    {
        return $this->getFieldRepetition(30, 0);
    }

    public function getRecurringServiceCode(): CWE
    {
        return $this->getFieldRepetition(31, 0);
    }

    public function getBillingMediaCode(): ID
    {
        return $this->getFieldRepetition(32, 0);
    }

    public function getExpectedSurgeryDateTime(): DTM
    {
        return $this->getFieldRepetition(33, 0);
    }

    public function getMilitaryPartnershipCode(): ID
    {
        return $this->getFieldRepetition(34, 0);
    }

    public function getMilitaryNonAvailabilityCode(): ID
    {
        return $this->getFieldRepetition(35, 0);
    }

    public function getNewbornBabyIndicator(): ID
    {
        return $this->getFieldRepetition(36, 0);
    }

    public function getBabyDetainedIndicator(): ID
    {
        return $this->getFieldRepetition(37, 0);
    }

    public function getModeOfArrivalCode(): CWE
    {
        return $this->getFieldRepetition(38, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getRecreationalDrugUseCode(): array
    {
        return $this->getField(39);
    }

    public function getAdmissionLevelOfCareCode(): CWE
    {
        return $this->getFieldRepetition(40, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getPrecautionCode(): array
    {
        return $this->getField(41);
    }

    public function getPatientConditionCode(): CWE
    {
        return $this->getFieldRepetition(42, 0);
    }

    public function getLivingWillCode(): CWE
    {
        return $this->getFieldRepetition(43, 0);
    }

    public function getOrganDonorCode(): CWE
    {
        return $this->getFieldRepetition(44, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getAdvanceDirectiveCode(): array
    {
        return $this->getField(45);
    }

    public function getPatientStatusEffectiveDate(): DT
    {
        return $this->getFieldRepetition(46, 0);
    }

    public function getExpectedLoaReturnDateTime(): DTM
    {
        return $this->getFieldRepetition(47, 0);
    }

    public function getExpectedPreAdmissionTestingDateTime(): DTM
    {
        return $this->getFieldRepetition(48, 0);
    }

    /**
     * @return list<CWE>
     */
    public function getNotifyClergyCode(): array
    {
        return $this->getField(49);
    }

    public function getAdvanceDirectiveLastVerifiedDate(): DT
    {
        return $this->getFieldRepetition(50, 0);
    }
}
