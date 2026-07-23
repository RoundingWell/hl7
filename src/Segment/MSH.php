<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Segment;

use Override;
use RoundingWell\HL7\AbstractSegment;
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
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidSegment;
use RoundingWell\HL7\TypeDefinition;

/**
 * Message Header Segment
 *
 * @mago-expect lint:too-many-methods
 */
final class MSH extends AbstractSegment
{
    public function __construct()
    {
        $this->add(
            new TypeDefinition(
                'Field Separator',
                ST::class,
                args: [
                    'minLength' => 1,
                    'maxLength' => 1,
                ],
                isRequired: true,
                maxReps: 1,
            ),
        );
        $this->add(
            new TypeDefinition(
                'Encoding Characters',
                ST::class,
                args: [
                    'minLength' => 4,
                    'maxLength' => 5,
                ],
                isRequired: true,
                maxReps: 1,
            ),
        );
        $this->add(new TypeDefinition('Sending Application', HD::class, maxReps: 1));
        $this->add(new TypeDefinition('Sending Facility', HD::class, maxReps: 1));
        $this->add(new TypeDefinition('Receiving Application', HD::class, maxReps: 1));
        $this->add(new TypeDefinition('Receiving Facility', HD::class, maxReps: 1));
        $this->add(new TypeDefinition('Date/Time of Message', DTM::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Security', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Message Type', MSG::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Message Control ID', ST::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Processing ID', PT::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Version ID', VID::class, isRequired: true, maxReps: 1));
        $this->add(new TypeDefinition('Sequence Number', NM::class, maxReps: 1));
        $this->add(new TypeDefinition('Continuation Pointer', ST::class, maxReps: 1));
        $this->add(new TypeDefinition('Accept Acknowledgement Type', ID::class, args: ['table' => 155], maxReps: 1));
        $this->add(
            new TypeDefinition('Application Acknowledgement Type', ID::class, args: ['table' => 155], maxReps: 1),
        );
        $this->add(new TypeDefinition('Country Code', ID::class, args: ['table' => 399], maxReps: 1));
        $this->add(new TypeDefinition('Character Set', ID::class, args: ['table' => 211]));
        $this->add(new TypeDefinition('Principal Language of Message', CWE::class, maxReps: 1));
        $this->add(
            new TypeDefinition(
                'Alternate Character Set Handling Scheme',
                ID::class,
                args: [
                    'table' => 356,
                ],
                maxReps: 1,
            ),
        );
        $this->add(new TypeDefinition('Message Profile Identifier', EI::class));
        $this->add(new TypeDefinition('Sending Responsible Organization', XON::class, maxReps: 1));
        $this->add(new TypeDefinition('Receiving Responsible Organization', XON::class, maxReps: 1));
        $this->add(new TypeDefinition('Sending Network Address', HD::class, maxReps: 1));
        $this->add(new TypeDefinition('Receiving Network Address', HD::class, maxReps: 1));
    }

    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        $fields = explode($encoding->fieldSeparator, $data);

        if (count($fields) < 2) {
            throw InvalidSegment::invalidMSH($data);
        }

        // Drop the "MSH" segment identifier.
        array_shift($fields);

        // MSH.1 MUST be the field separator, and it MUST be stored verbatim.
        $this->getFieldSeparator()->setValue($encoding->fieldSeparator);

        // MSH.2 MUST be the encoding characters, and it MUST be stored verbatim.
        // @mago-expect analysis:possibly-null-argument
        $this->getEncodingCharacters()->setValue(array_shift($fields));

        // The remaining values map to MSH.3 onward.
        foreach ($fields as $idx => $field) {
            foreach (explode($encoding->repetitionSeparator, $field) as $rep => $value) {
                // $idx starts at 0 for MSH.3, so the field number is always 3 or greater.
                $this->getFieldRepetition($idx + 3, $rep)->parse($encoding, $value);
            }
        }
    }

    #[Override]
    public function serialize(Encoding $encoding): string
    {
        // The values for MSH.1 and MSH.2 MUST come from the encoding object, since all
        // subsequent fields are encoded using these characters.
        $header = 'MSH' . $encoding->fieldSeparator . $encoding->encodingCharacters() . $encoding->fieldSeparator;

        return trim($header . $this->serializeFields($encoding, 3), $encoding->fieldSeparator);
    }

    /**
     * MSH.1 Field Separator
     */
    public function getFieldSeparator(): ST
    {
        return $this->getFieldRepetition(1, 0);
    }

    /**
     * MSH.2 Encoding Characters
     */
    public function getEncodingCharacters(): ST
    {
        return $this->getFieldRepetition(2, 0);
    }

    /**
     * MSH.3 Sending Application
     */
    public function getSendingApplication(): HD
    {
        return $this->getFieldRepetition(3, 0);
    }

    /**
     * MSH.4 Sending Facility
     */
    public function getSendingFacility(): HD
    {
        return $this->getFieldRepetition(4, 0);
    }

    /**
     * MSH.5 Receiving Application
     */
    public function getReceivingApplication(): HD
    {
        return $this->getFieldRepetition(5, 0);
    }

    /**
     * MSH.6 Receiving Facility
     */
    public function getReceivingFacility(): HD
    {
        return $this->getFieldRepetition(6, 0);
    }

    /**
     * MSH.7 Date/Time of Message
     */
    public function getDateTimeOfMessage(): DTM
    {
        return $this->getFieldRepetition(7, 0);
    }

    /**
     * MSH.8 Security
     */
    public function getSecurity(): ST
    {
        return $this->getFieldRepetition(8, 0);
    }

    /**
     * MSH.9 Message Type
     */
    public function getMessageType(): MSG
    {
        return $this->getFieldRepetition(9, 0);
    }

    /**
     * MSH.10 Message Control ID
     */
    public function getMessageControlId(): ST
    {
        return $this->getFieldRepetition(10, 0);
    }

    /**
     * MSH.11 Processing ID
     */
    public function getProcessingId(): PT
    {
        return $this->getFieldRepetition(11, 0);
    }

    /**
     * MSH.12 Version ID
     */
    public function getVersionId(): VID
    {
        return $this->getFieldRepetition(12, 0);
    }

    /**
     * MSH.13 Sequence Number
     */
    public function getSequenceNumber(): NM
    {
        return $this->getFieldRepetition(13, 0);
    }

    /**
     * MSH.14 Continuation Pointer
     */
    public function getContinuationPointer(): ST
    {
        return $this->getFieldRepetition(14, 0);
    }

    /**
     * MSH.15 Accept Acknowledgement Type
     */
    public function getAcceptAcknowledgementType(): ID
    {
        return $this->getFieldRepetition(15, 0);
    }

    /**
     * MSH.16 Application Acknowledgement Type
     */
    public function getApplicationAcknowledgementType(): ID
    {
        return $this->getFieldRepetition(16, 0);
    }

    /**
     * MSH.17 Country Code
     */
    public function getCountryCode(): ID
    {
        return $this->getFieldRepetition(17, 0);
    }

    /**
     * MSH.18 Character Set
     *
     * @return list<ID>
     */
    public function getCharacterSet(): array
    {
        return $this->getField(18);
    }

    /**
     * MSH.19 Principal Language of Message
     */
    public function getPrincipalLanguageOfMessage(): CWE
    {
        return $this->getFieldRepetition(19, 0);
    }

    /**
     * MSH.20 Alternate Character Set Handling Scheme
     */
    public function getAlternateCharacterSetHandlingScheme(): ID
    {
        return $this->getFieldRepetition(20, 0);
    }

    /**
     * MSH.21 Message Profile Identifier
     *
     * @return list<EI>
     */
    public function getMessageProfileIdentifier(): array
    {
        return $this->getField(21);
    }

    /**
     * MSH.22 Sending Responsible Organization
     */
    public function getSendingResponsibleOrganization(): XON
    {
        return $this->getFieldRepetition(22, 0);
    }

    /**
     * MSH.23 Receiving Responsible Organization
     */
    public function getReceivingResponsibleOrganization(): XON
    {
        return $this->getFieldRepetition(23, 0);
    }

    /**
     * MSH.24 Sending Network Address
     */
    public function getSendingNetworkAddress(): HD
    {
        return $this->getFieldRepetition(24, 0);
    }

    /**
     * MSH.25 Receiving Network Address
     */
    public function getReceivingNetworkAddress(): HD
    {
        return $this->getFieldRepetition(25, 0);
    }
}
