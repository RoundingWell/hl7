<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\BaseField;
use RoundingWell\HL7\BaseSegment;
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

/**
 * Patient Visit Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class PV1 extends BaseSegment
{
    /**
     * @mago-expect lint:halstead
     */
    public function __construct()
    {
        parent::__construct('PV1');

        $this->addField(1, new BaseField('Set ID', SI::class));
        $this->addField(2, new BaseField('Patient Class', CWE::class, required: true));
        $this->addField(3, new BaseField('Assigned Patient Location', PL::class));
        $this->addField(4, new BaseField('Admission Type', CWE::class));
        $this->addField(5, new BaseField('Preadmit Number', CX::class));
        $this->addField(6, new BaseField('Prior Patient Location', PL::class));
        $this->addField(7, new BaseField('Attending Doctor', XCN::class, repeating: true));
        $this->addField(8, new BaseField('Referring Doctor', XCN::class, repeating: true));
        $this->addField(9, new BaseField('Consulting Doctor', XCN::class, repeating: true));
        $this->addField(10, new BaseField('Hospital Service', CWE::class));
        $this->addField(11, new BaseField('Temporary Location', PL::class));
        $this->addField(12, new BaseField('Preadmit Test Indicator', CWE::class));
        $this->addField(13, new BaseField('Re-admission Indicator', CWE::class));
        $this->addField(14, new BaseField('Admit Source', CWE::class));
        $this->addField(15, new BaseField('Ambulatory Status', CWE::class, repeating: true));
        $this->addField(16, new BaseField('VIP Indicator', CWE::class));
        $this->addField(17, new BaseField('Admitting Doctor', XCN::class, repeating: true));
        $this->addField(18, new BaseField('Patient Type', CWE::class));
        $this->addField(19, new BaseField('Visit Number', CX::class));
        $this->addField(20, new BaseField('Financial Class', FC::class, repeating: true));
        $this->addField(21, new BaseField('Charge Price Indicator', CWE::class));
        $this->addField(22, new BaseField('Courtesy Code', CWE::class));
        $this->addField(23, new BaseField('Credit Rating', CWE::class));
        $this->addField(24, new BaseField('Contract Code', CWE::class, repeating: true));
        $this->addField(25, new BaseField('Contract Effective Date', DT::class, repeating: true));
        $this->addField(26, new BaseField('Contract Amount', NM::class, repeating: true));
        $this->addField(27, new BaseField('Contract Period', NM::class, repeating: true));
        $this->addField(28, new BaseField('Interest Code', CWE::class));
        $this->addField(29, new BaseField('Transfer to Bad Debt Code', CWE::class));
        $this->addField(30, new BaseField('Transfer to Bad Debt Date', DT::class));
        $this->addField(31, new BaseField('Bad Debt Agency Code', CWE::class));
        $this->addField(32, new BaseField('Bad Debt Transfer Amount', NM::class));
        $this->addField(33, new BaseField('Bad Debt Recovery Amount', NM::class));
        $this->addField(34, new BaseField('Delete Account Indicator', CWE::class));
        $this->addField(35, new BaseField('Delete Account Date', DT::class));
        $this->addField(36, new BaseField('Discharge Disposition', CWE::class));
        $this->addField(37, new BaseField('Discharged to Location', DLD::class));
        $this->addField(38, new BaseField('Diet Type', CWE::class));
        $this->addField(39, new BaseField('Servicing Facility', CWE::class));
        $this->addField(40, new BaseField('Bed Status', CWE::class));
        $this->addField(41, new BaseField('Account Status', CWE::class));
        $this->addField(42, new BaseField('Pending Location', PL::class));
        $this->addField(43, new BaseField('Prior Temporary Location', PL::class));
        $this->addField(44, new BaseField('Admit Date/Time', DTM::class));
        $this->addField(45, new BaseField('Discharge Date/Time', DTM::class));
        $this->addField(46, new BaseField('Current Patient Balance', NM::class));
        $this->addField(47, new BaseField('Total Charges', NM::class));
        $this->addField(48, new BaseField('Total Adjustments', NM::class));
        $this->addField(49, new BaseField('Total Payments', NM::class));
        $this->addField(50, new BaseField('Alternate Visit ID', CX::class, repeating: true));
        $this->addField(51, new BaseField('Visit Indicator', CWE::class));
        $this->addField(52, new BaseField('Other Healthcare Provider', ST::class));
        $this->addField(53, new BaseField('Service Episode Description', ST::class));
        $this->addField(54, new BaseField('Service Episode Identifier', CX::class));
    }

    public function getIdentity(): SI
    {
        return $this->getField(1)->getInstance();
    }

    public function getPatientClass(): CWE
    {
        return $this->getField(2)->getInstance();
    }

    public function getAssignedPatientLocation(): PL
    {
        return $this->getField(3)->getInstance();
    }

    public function getAdmissionType(): CWE
    {
        return $this->getField(4)->getInstance();
    }

    public function getPreadmitNumber(): CX
    {
        return $this->getField(5)->getInstance();
    }

    public function getPriorPatientLocation(): PL
    {
        return $this->getField(6)->getInstance();
    }

    /**
     * @return list<XCN>
     */
    public function getAttendingDoctor(): array
    {
        return $this->getField(7)->getInstance();
    }

    /**
     * @return list<XCN>
     */
    public function getReferringDoctor(): array
    {
        return $this->getField(8)->getInstance();
    }

    /**
     * @return list<XCN>
     */
    public function getConsultingDoctor(): array
    {
        return $this->getField(9)->getInstance();
    }

    public function getHospitalService(): CWE
    {
        return $this->getField(10)->getInstance();
    }

    public function getTemporaryLocation(): PL
    {
        return $this->getField(11)->getInstance();
    }

    public function getPreadmitTestIndicator(): CWE
    {
        return $this->getField(12)->getInstance();
    }

    public function getReadmissionIndicator(): CWE
    {
        return $this->getField(13)->getInstance();
    }

    public function getAdmitSource(): CWE
    {
        return $this->getField(14)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getAmbulatoryStatus(): array
    {
        return $this->getField(15)->getInstance();
    }

    public function getVipIndicator(): CWE
    {
        return $this->getField(16)->getInstance();
    }

    /**
     * @return list<XCN>
     */
    public function getAdmittingDoctor(): array
    {
        return $this->getField(17)->getInstance();
    }

    public function getPatientType(): CWE
    {
        return $this->getField(18)->getInstance();
    }

    public function getVisitNumber(): CX
    {
        return $this->getField(19)->getInstance();
    }

    /**
     * @return list<FC>
     */
    public function getFinancialClass(): array
    {
        return $this->getField(20)->getInstance();
    }

    public function getChargePriceIndicator(): CWE
    {
        return $this->getField(21)->getInstance();
    }

    public function getCourtesyCode(): CWE
    {
        return $this->getField(22)->getInstance();
    }

    public function getCreditRating(): CWE
    {
        return $this->getField(23)->getInstance();
    }

    /**
     * @return list<CWE>
     */
    public function getContractCode(): array
    {
        return $this->getField(24)->getInstance();
    }

    /**
     * @return list<DT>
     */
    public function getContractEffectiveDate(): array
    {
        return $this->getField(25)->getInstance();
    }

    /**
     * @return list<NM>
     */
    public function getContractAmount(): array
    {
        return $this->getField(26)->getInstance();
    }

    /**
     * @return list<NM>
     */
    public function getContractPeriod(): array
    {
        return $this->getField(27)->getInstance();
    }

    public function getInterestCode(): CWE
    {
        return $this->getField(28)->getInstance();
    }

    public function getTransferToBadDebtCode(): CWE
    {
        return $this->getField(29)->getInstance();
    }

    public function getTransferToBadDebtDate(): DT
    {
        return $this->getField(30)->getInstance();
    }

    public function getBadDebtAgencyCode(): CWE
    {
        return $this->getField(31)->getInstance();
    }

    public function getBadDebtTransferAmount(): NM
    {
        return $this->getField(32)->getInstance();
    }

    public function getBadDebtRecoveryAmount(): NM
    {
        return $this->getField(33)->getInstance();
    }

    public function getDeleteAccountIndicator(): CWE
    {
        return $this->getField(34)->getInstance();
    }

    public function getDeleteAccountDate(): DT
    {
        return $this->getField(35)->getInstance();
    }

    public function getDischargeDisposition(): CWE
    {
        return $this->getField(36)->getInstance();
    }

    public function getDischargedToLocation(): DLD
    {
        return $this->getField(37)->getInstance();
    }

    public function getDietType(): CWE
    {
        return $this->getField(38)->getInstance();
    }

    public function getServicingFacility(): CWE
    {
        return $this->getField(39)->getInstance();
    }

    public function getBedStatus(): CWE
    {
        return $this->getField(40)->getInstance();
    }

    public function getAccountStatus(): CWE
    {
        return $this->getField(41)->getInstance();
    }

    public function getPendingLocation(): PL
    {
        return $this->getField(42)->getInstance();
    }

    public function getPriorTemporaryLocation(): PL
    {
        return $this->getField(43)->getInstance();
    }

    public function getAdmitDateTime(): DTM
    {
        return $this->getField(44)->getInstance();
    }

    public function getDischargeDateTime(): DTM
    {
        return $this->getField(45)->getInstance();
    }

    public function getCurrentPatientBalance(): NM
    {
        return $this->getField(46)->getInstance();
    }

    public function getTotalCharges(): NM
    {
        return $this->getField(47)->getInstance();
    }

    public function getTotalAdjustments(): NM
    {
        return $this->getField(48)->getInstance();
    }

    public function getTotalPayments(): NM
    {
        return $this->getField(49)->getInstance();
    }

    /**
     * @return list<CX>
     */
    public function getAlternateVisitId(): array
    {
        return $this->getField(50)->getInstance();
    }

    public function getVisitIndicator(): CWE
    {
        return $this->getField(51)->getInstance();
    }

    public function getOtherHealthcareProvider(): ST
    {
        return $this->getField(52)->getInstance();
    }

    public function getServiceEpisodeDescription(): ST
    {
        return $this->getField(53)->getInstance();
    }

    public function getServiceEpisodeIdentifier(): CX
    {
        return $this->getField(54)->getInstance();
    }
}
