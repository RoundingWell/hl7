<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

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
use RoundingWell\HL7\Field;
use RoundingWell\HL7\Segment;

/**
 * Patient Visit Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class PV1 extends Segment
{
    /**
     * @mago-expect lint:halstead
     */
    public function __construct()
    {
        parent::__construct('PV1');

        $this->addField(1, new Field('Set ID', SI::class));
        $this->addField(2, new Field('Patient Class', CWE::class, required: true));
        $this->addField(3, new Field('Assigned Patient Location', PL::class));
        $this->addField(4, new Field('Admission Type', CWE::class));
        $this->addField(5, new Field('Preadmit Number', CX::class));
        $this->addField(6, new Field('Prior Patient Location', PL::class));
        $this->addField(7, new Field('Attending Doctor', XCN::class, repeating: true));
        $this->addField(8, new Field('Referring Doctor', XCN::class, repeating: true));
        $this->addField(9, new Field('Consulting Doctor', XCN::class, repeating: true));
        $this->addField(10, new Field('Hospital Service', CWE::class));
        $this->addField(11, new Field('Temporary Location', PL::class));
        $this->addField(12, new Field('Preadmit Test Indicator', CWE::class));
        $this->addField(13, new Field('Re-admission Indicator', CWE::class));
        $this->addField(14, new Field('Admit Source', CWE::class));
        $this->addField(15, new Field('Ambulatory Status', CWE::class, repeating: true));
        $this->addField(16, new Field('VIP Indicator', CWE::class));
        $this->addField(17, new Field('Admitting Doctor', XCN::class, repeating: true));
        $this->addField(18, new Field('Patient Type', CWE::class));
        $this->addField(19, new Field('Visit Number', CX::class));
        $this->addField(20, new Field('Financial Class', FC::class, repeating: true));
        $this->addField(21, new Field('Charge Price Indicator', CWE::class));
        $this->addField(22, new Field('Courtesy Code', CWE::class));
        $this->addField(23, new Field('Credit Rating', CWE::class));
        $this->addField(24, new Field('Contract Code', CWE::class, repeating: true));
        $this->addField(25, new Field('Contract Effective Date', DT::class, repeating: true));
        $this->addField(26, new Field('Contract Amount', NM::class, repeating: true));
        $this->addField(27, new Field('Contract Period', NM::class, repeating: true));
        $this->addField(28, new Field('Interest Code', CWE::class));
        $this->addField(29, new Field('Transfer to Bad Debt Code', CWE::class));
        $this->addField(30, new Field('Transfer to Bad Debt Date', DT::class));
        $this->addField(31, new Field('Bad Debt Agency Code', CWE::class));
        $this->addField(32, new Field('Bad Debt Transfer Amount', NM::class));
        $this->addField(33, new Field('Bad Debt Recovery Amount', NM::class));
        $this->addField(34, new Field('Delete Account Indicator', CWE::class));
        $this->addField(35, new Field('Delete Account Date', DT::class));
        $this->addField(36, new Field('Discharge Disposition', CWE::class));
        $this->addField(37, new Field('Discharged to Location', DLD::class));
        $this->addField(38, new Field('Diet Type', CWE::class));
        $this->addField(39, new Field('Servicing Facility', CWE::class));
        $this->addField(40, new Field('Bed Status', CWE::class));
        $this->addField(41, new Field('Account Status', CWE::class));
        $this->addField(42, new Field('Pending Location', PL::class));
        $this->addField(43, new Field('Prior Temporary Location', PL::class));
        $this->addField(44, new Field('Admit Date/Time', DTM::class));
        $this->addField(45, new Field('Discharge Date/Time', DTM::class));
        $this->addField(46, new Field('Current Patient Balance', NM::class));
        $this->addField(47, new Field('Total Charges', NM::class));
        $this->addField(48, new Field('Total Adjustments', NM::class));
        $this->addField(49, new Field('Total Payments', NM::class));
        $this->addField(50, new Field('Alternate Visit ID', CX::class, repeating: true));
        $this->addField(51, new Field('Visit Indicator', CWE::class));
        $this->addField(52, new Field('Other Healthcare Provider', ST::class));
        $this->addField(53, new Field('Service Episode Description', ST::class));
        $this->addField(54, new Field('Service Episode Identifier', CX::class));
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
