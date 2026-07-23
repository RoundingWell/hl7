<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\AbstractSegment;
use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\CX;
use RoundingWell\HL7\DataType\DLD;
use RoundingWell\HL7\DataType\DT;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\FC;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\PL;
use RoundingWell\HL7\DataType\SI;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\XCN;
use RoundingWell\HL7\TypeDefinition;

/**
 * Patient Visit Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class PV1 extends AbstractSegment
{
    public function __construct()
    {
        $this->add(new TypeDefinition('Set ID', SI::class, maxReps: 1));
        $this->add(new TypeDefinition('Patient Class', CWE::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Assigned Patient Location', PL::class, maxReps: 1));
        $this->add(new TypeDefinition('Admission Type', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Preadmit Number', CX::class, maxReps: 1));
        $this->add(new TypeDefinition('Prior Patient Location', PL::class, maxReps: 1));
        $this->add(new TypeDefinition('Attending Doctor', XCN::class));
        $this->add(new TypeDefinition('Referring Doctor', XCN::class));
        $this->add(new TypeDefinition('Consulting Doctor', XCN::class));
        $this->add(new TypeDefinition('Hospital Service', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Temporary Location', PL::class, maxReps: 1));
        $this->add(new TypeDefinition('Preadmit Test Indicator', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Re-admission Indicator', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Admit Source', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Ambulatory Status', CWE::class));
        $this->add(new TypeDefinition('VIP Indicator', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Admitting Doctor', XCN::class));
        $this->add(new TypeDefinition('Patient Type', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Visit Number', CX::class, maxReps: 1));
        $this->add(new TypeDefinition('Financial Class', FC::class));
        $this->add(new TypeDefinition('Charge Price Indicator', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Courtesy Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Credit Rating', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Contract Code', CWE::class));
        $this->add(new TypeDefinition('Contract Effective Date', DT::class));
        $this->add(new TypeDefinition('Contract Amount', NM::class));
        $this->add(new TypeDefinition('Contract Period', NM::class));
        $this->add(new TypeDefinition('Interest Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Transfer to Bad Debt Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Transfer to Bad Debt Date', DT::class, maxReps: 1));
        $this->add(new TypeDefinition('Bad Debt Agency Code', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Bad Debt Transfer Amount', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Bad Debt Recovery Amount', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Delete Account Indicator', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Delete Account Date', DT::class, maxReps: 1));
        $this->add(new TypeDefinition('Discharge Disposition', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Discharged to Location', DLD::class, maxReps: 1));
        $this->add(new TypeDefinition('Diet Type', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Servicing Facility', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Bed Status', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Account Status', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Pending Location', PL::class, maxReps: 1));
        $this->add(new TypeDefinition('Prior Temporary Location', PL::class, maxReps: 1));
        $this->add(new TypeDefinition('Admit Date/Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Discharge Date/Time', DTM::class, maxReps: 1));
        $this->add(new TypeDefinition('Current Patient Balance', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Total Charges', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Total Adjustments', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Total Payments', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Alternate Visit ID', CX::class));
        $this->add(new TypeDefinition('Visit Indicator', CWE::class, maxReps: 1));
        $this->add(new TypeDefinition('Other Healthcare Provider', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Service Episode Description', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Service Episode Identifier', CX::class, maxReps: 1));
    }

    /**
     * PV1.1 Set ID
     */
    public function getIdentity(): SI
    {
        return $this->getFieldRepetition(1, 0);
    }

    /**
     * PV1.2 Patient Class
     */
    public function getPatientClass(): CWE
    {
        return $this->getFieldRepetition(2, 0);
    }

    /**
     * PV1.3 Assigned Patient Location
     */
    public function getAssignedPatientLocation(): PL
    {
        return $this->getFieldRepetition(3, 0);
    }

    /**
     * PV1.4 Admission Type
     */
    public function getAdmissionType(): CWE
    {
        return $this->getFieldRepetition(4, 0);
    }

    /**
     * PV1.5 Preadmit Number
     */
    public function getPreadmitNumber(): CX
    {
        return $this->getFieldRepetition(5, 0);
    }

    /**
     * PV1.6 Prior Patient Location
     */
    public function getPriorPatientLocation(): PL
    {
        return $this->getFieldRepetition(6, 0);
    }

    /**
     * PV1.7 Attending Doctor
     *
     * @return list<XCN>
     */
    public function getAttendingDoctor(): array
    {
        return $this->getField(7);
    }

    /**
     * PV1.8 Referring Doctor
     *
     * @return list<XCN>
     */
    public function getReferringDoctor(): array
    {
        return $this->getField(8);
    }

    /**
     * PV1.9 Consulting Doctor
     *
     * @return list<XCN>
     */
    public function getConsultingDoctor(): array
    {
        return $this->getField(9);
    }

    /**
     * PV1.10 Hospital Service
     */
    public function getHospitalService(): CWE
    {
        return $this->getFieldRepetition(10, 0);
    }

    /**
     * PV1.11 Temporary Location
     */
    public function getTemporaryLocation(): PL
    {
        return $this->getFieldRepetition(11, 0);
    }

    /**
     * PV1.12 Preadmit Test Indicator
     */
    public function getPreadmitTestIndicator(): CWE
    {
        return $this->getFieldRepetition(12, 0);
    }

    /**
     * PV1.13 Re-admission Indicator
     */
    public function getReadmissionIndicator(): CWE
    {
        return $this->getFieldRepetition(13, 0);
    }

    /**
     * PV1.14 Admit Source
     */
    public function getAdmitSource(): CWE
    {
        return $this->getFieldRepetition(14, 0);
    }

    /**
     * PV1.15 Ambulatory Status
     *
     * @return list<CWE>
     */
    public function getAmbulatoryStatus(): array
    {
        return $this->getField(15);
    }

    /**
     * PV1.16 VIP Indicator
     */
    public function getVipIndicator(): CWE
    {
        return $this->getFieldRepetition(16, 0);
    }

    /**
     * PV1.17 Admitting Doctor
     *
     * @return list<XCN>
     */
    public function getAdmittingDoctor(): array
    {
        return $this->getField(17);
    }

    /**
     * PV1.18 Patient Type
     */
    public function getPatientType(): CWE
    {
        return $this->getFieldRepetition(18, 0);
    }

    /**
     * PV1.19 Visit Number
     */
    public function getVisitNumber(): CX
    {
        return $this->getFieldRepetition(19, 0);
    }

    /**
     * PV1.20 Financial Class
     *
     * @return list<FC>
     */
    public function getFinancialClass(): array
    {
        return $this->getField(20);
    }

    /**
     * PV1.21 Charge Price Indicator
     */
    public function getChargePriceIndicator(): CWE
    {
        return $this->getFieldRepetition(21, 0);
    }

    /**
     * PV1.22 Courtesy Code
     */
    public function getCourtesyCode(): CWE
    {
        return $this->getFieldRepetition(22, 0);
    }

    /**
     * PV1.23 Credit Rating
     */
    public function getCreditRating(): CWE
    {
        return $this->getFieldRepetition(23, 0);
    }

    /**
     * PV1.24 Contract Code
     *
     * @return list<CWE>
     */
    public function getContractCode(): array
    {
        return $this->getField(24);
    }

    /**
     * PV1.25 Contract Effective Date
     *
     * @return list<DT>
     */
    public function getContractEffectiveDate(): array
    {
        return $this->getField(25);
    }

    /**
     * PV1.26 Contract Amount
     *
     * @return list<NM>
     */
    public function getContractAmount(): array
    {
        return $this->getField(26);
    }

    /**
     * PV1.27 Contract Period
     *
     * @return list<NM>
     */
    public function getContractPeriod(): array
    {
        return $this->getField(27);
    }

    /**
     * PV1.28 Interest Code
     */
    public function getInterestCode(): CWE
    {
        return $this->getFieldRepetition(28, 0);
    }

    /**
     * PV1.29 Transfer to Bad Debt Code
     */
    public function getTransferToBadDebtCode(): CWE
    {
        return $this->getFieldRepetition(29, 0);
    }

    /**
     * PV1.30 Transfer to Bad Debt Date
     */
    public function getTransferToBadDebtDate(): DT
    {
        return $this->getFieldRepetition(30, 0);
    }

    /**
     * PV1.31 Bad Debt Agency Code
     */
    public function getBadDebtAgencyCode(): CWE
    {
        return $this->getFieldRepetition(31, 0);
    }

    /**
     * PV1.32 Bad Debt Transfer Amount
     */
    public function getBadDebtTransferAmount(): NM
    {
        return $this->getFieldRepetition(32, 0);
    }

    /**
     * PV1.33 Bad Debt Recovery Amount
     */
    public function getBadDebtRecoveryAmount(): NM
    {
        return $this->getFieldRepetition(33, 0);
    }

    /**
     * PV1.34 Delete Account Indicator
     */
    public function getDeleteAccountIndicator(): CWE
    {
        return $this->getFieldRepetition(34, 0);
    }

    /**
     * PV1.35 Delete Account Date
     */
    public function getDeleteAccountDate(): DT
    {
        return $this->getFieldRepetition(35, 0);
    }

    /**
     * PV1.36 Discharge Disposition
     */
    public function getDischargeDisposition(): CWE
    {
        return $this->getFieldRepetition(36, 0);
    }

    /**
     * PV1.37 Discharged to Location
     */
    public function getDischargedToLocation(): DLD
    {
        return $this->getFieldRepetition(37, 0);
    }

    /**
     * PV1.38 Diet Type
     */
    public function getDietType(): CWE
    {
        return $this->getFieldRepetition(38, 0);
    }

    /**
     * PV1.39 Servicing Facility
     */
    public function getServicingFacility(): CWE
    {
        return $this->getFieldRepetition(39, 0);
    }

    /**
     * PV1.40 Bed Status
     */
    public function getBedStatus(): CWE
    {
        return $this->getFieldRepetition(40, 0);
    }

    /**
     * PV1.41 Account Status
     */
    public function getAccountStatus(): CWE
    {
        return $this->getFieldRepetition(41, 0);
    }

    /**
     * PV1.42 Pending Location
     */
    public function getPendingLocation(): PL
    {
        return $this->getFieldRepetition(42, 0);
    }

    /**
     * PV1.43 Prior Temporary Location
     */
    public function getPriorTemporaryLocation(): PL
    {
        return $this->getFieldRepetition(43, 0);
    }

    /**
     * PV1.44 Admit Date/Time
     */
    public function getAdmitDateTime(): DTM
    {
        return $this->getFieldRepetition(44, 0);
    }

    /**
     * PV1.45 Discharge Date/Time
     */
    public function getDischargeDateTime(): DTM
    {
        return $this->getFieldRepetition(45, 0);
    }

    /**
     * PV1.46 Current Patient Balance
     */
    public function getCurrentPatientBalance(): NM
    {
        return $this->getFieldRepetition(46, 0);
    }

    /**
     * PV1.47 Total Charges
     */
    public function getTotalCharges(): NM
    {
        return $this->getFieldRepetition(47, 0);
    }

    /**
     * PV1.48 Total Adjustments
     */
    public function getTotalAdjustments(): NM
    {
        return $this->getFieldRepetition(48, 0);
    }

    /**
     * PV1.49 Total Payments
     */
    public function getTotalPayments(): NM
    {
        return $this->getFieldRepetition(49, 0);
    }

    /**
     * PV1.50 Alternate Visit ID
     *
     * @return list<CX>
     */
    public function getAlternateVisitId(): array
    {
        return $this->getField(50);
    }

    /**
     * PV1.51 Visit Indicator
     */
    public function getVisitIndicator(): CWE
    {
        return $this->getFieldRepetition(51, 0);
    }

    /**
     * PV1.52 Other Healthcare Provider
     */
    public function getOtherHealthcareProvider(): ST
    {
        return $this->getFieldRepetition(52, 0);
    }

    /**
     * PV1.53 Service Episode Description
     */
    public function getServiceEpisodeDescription(): ST
    {
        return $this->getFieldRepetition(53, 0);
    }

    /**
     * PV1.54 Service Episode Identifier
     */
    public function getServiceEpisodeIdentifier(): CX
    {
        return $this->getFieldRepetition(54, 0);
    }
}
