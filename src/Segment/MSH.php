<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use RoundingWell\HL7\DataType\CWE;
use RoundingWell\HL7\DataType\DTM;
use RoundingWell\HL7\DataType\EI;
use RoundingWell\HL7\DataType\HD;
use RoundingWell\HL7\DataType\ID;
use RoundingWell\HL7\DataType\MSG;
use RoundingWell\HL7\DataType\NM;
use RoundingWell\HL7\DataType\PT;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\DataType\VID;
use RoundingWell\HL7\DataType\XON;
use RoundingWell\HL7\Field;
use RoundingWell\HL7\Segment;

/**
 * Message Header Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class MSH extends Segment
{
    public function __construct()
    {
        parent::__construct('MSH');

        $this->addField(1, new Field('Field Separator', ST::class, required: true, args: [
            'minLength' => 1,
            'maxLength' => 1,
        ]));
        $this->addField(2, new Field('Encoding Characters', ST::class, required: true, args: [
            'minLength' => 4,
            'maxLength' => 4,
        ]));
        $this->addField(3, new Field('Sending Application', HD::class));
        $this->addField(4, new Field('Sending Facility', HD::class));
        $this->addField(5, new Field('Receiving Application', HD::class));
        $this->addField(6, new Field('Receiving Facility', HD::class));
        $this->addField(7, new Field('Date/Time of Message', DTM::class, required: true));
        $this->addField(8, new Field('Security', ST::class));
        $this->addField(9, new Field('Message Type', MSG::class, required: true));
        $this->addField(10, new Field('Message Control ID', ST::class, required: true));
        $this->addField(11, new Field('Processing ID', PT::class, required: true));
        $this->addField(12, new Field('Version ID', VID::class, required: true));
        $this->addField(13, new Field('Sequence Number', NM::class));
        $this->addField(14, new Field('Continuation Pointer', ST::class));
        $this->addField(15, new Field('Accept Acknowledgement Type', ID::class, args: ['table' => 155]));
        $this->addField(16, new Field('Application Acknowledgement Type', ID::class, args: ['table' => 155]));
        $this->addField(17, new Field('Country Code', ID::class, args: ['table' => 399]));
        $this->addField(18, new Field('Character Set', ID::class, repeating: true, args: ['table' => 211]));
        $this->addField(19, new Field('Principal Language of Message', CWE::class));
        $this->addField(20, new Field('Alternate Character Set Handling Scheme', ID::class, args: ['table' => 356]));
        $this->addField(21, new Field('Message Profile Identifier', EI::class, repeating: true));
        $this->addField(22, new Field('Sending Responsible Organization', XON::class));
        $this->addField(23, new Field('Receiving Responsible Organization', XON::class));
        $this->addField(24, new Field('Sending Network Address', HD::class));
        $this->addField(25, new Field('Receiving Network Address', HD::class));
    }

    public function getFieldSeparator(): ST
    {
        return $this->getField(1)->getInstance();
    }

    public function getComponentSeparator(): ST
    {
        return $this->getField(2)->getInstance();
    }

    public function getSendingApplication(): HD
    {
        return $this->getField(3)->getInstance();
    }

    public function getSendingFacility(): HD
    {
        return $this->getField(4)->getInstance();
    }

    public function getReceivingApplication(): HD
    {
        return $this->getField(5)->getInstance();
    }

    public function getReceivingFacility(): HD
    {
        return $this->getField(6)->getInstance();
    }

    public function getDateTimeOfMessage(): DTM
    {
        return $this->getField(7)->getInstance();
    }

    public function getSecurity(): ST
    {
        return $this->getField(8)->getInstance();
    }

    public function getMessageType(): MSG
    {
        return $this->getField(9)->getInstance();
    }

    public function getMessageControlId(): ST
    {
        return $this->getField(10)->getInstance();
    }

    public function getProcessingId(): PT
    {
        return $this->getField(11)->getInstance();
    }

    public function getVersionId(): VID
    {
        return $this->getField(12)->getInstance();
    }

    public function getSequenceNumber(): NM
    {
        return $this->getField(13)->getInstance();
    }

    public function getContinuationPointer(): ST
    {
        return $this->getField(14)->getInstance();
    }

    public function getAcceptAcknowledgementType(): ID
    {
        return $this->getField(15)->getInstance();
    }

    public function getApplicationAcknowledgementType(): ID
    {
        return $this->getField(16)->getInstance();
    }

    public function getCountryCode(): ID
    {
        return $this->getField(17)->getInstance();
    }

    /**
     * @return list<ID>
     */
    public function getCharacterSet(): array
    {
        return $this->getField(18)->getInstance();
    }

    public function getPrincipalLanguageOfMessage(): CWE
    {
        return $this->getField(19)->getInstance();
    }

    public function getAlternateCharacterSetHandlingScheme(): ID
    {
        return $this->getField(20)->getInstance();
    }

    /**
     * @return list<EI>
     */
    public function getMessageProfileIdentifier(): array
    {
        return $this->getField(21)->getInstance();
    }

    public function getSendingResponsibleOrganization(): XON
    {
        return $this->getField(22)->getInstance();
    }

    public function getReceivingResponsibleOrganization(): XON
    {
        return $this->getField(23)->getInstance();
    }

    public function getSendingNetworkAddress(): HD
    {
        return $this->getField(24)->getInstance();
    }

    public function getReceivingNetworkAddress(): HD
    {
        return $this->getField(25)->getInstance();
    }
}
