<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Segment\MSH;

#[CoversClass(MSH::class)]
final class MSHTest extends TestCase
{
    private MSH $msh;

    #[Override]
    protected function setUp(): void
    {
        $this->msh = new MSH();
        $this->msh->setRaw(new Encoding(), [
            '|', // MSH.1  Field Separator
            '^~\\&', // MSH.2  Encoding Characters
            'AccMgr^App^ISO', // MSH.3  Sending Application
            'SendFac', // MSH.4  Sending Facility
            'RecvApp', // MSH.5  Receiving Application
            'RecvFac', // MSH.6  Receiving Facility
            '20050110045504', // MSH.7  Date/Time of Message
            'SECRET', // MSH.8  Security
            'ADT^A01^ADT_A01', // MSH.9  Message Type
            '599102', // MSH.10 Message Control ID
            'P^A', // MSH.11 Processing ID
            '2.8', // MSH.12 Version ID
            '42', // MSH.13 Sequence Number
            'CONT', // MSH.14 Continuation Pointer
            'AL', // MSH.15 Accept Acknowledgement Type
            'NE', // MSH.16 Application Acknowledgement Type
            'USA', // MSH.17 Country Code
            'ASCII~UNICODE', // MSH.18 Character Set (repeating)
            'en^English^ISO639', // MSH.19 Principal Language of Message
            '2', // MSH.20 Alternate Character Set Handling Scheme
            'PROF1~PROF2', // MSH.21 Message Profile Identifier (repeating)
            'SendOrg', // MSH.22 Sending Responsible Organization
            'RecvOrg', // MSH.23 Receiving Responsible Organization
            '192.168.1.1^^IP', // MSH.24 Sending Network Address
            '10.0.0.1^^IP', // MSH.25 Receiving Network Address
        ]);
    }

    public function testEncodingFieldsExposeTheDelimitersUsedToParseTheMessage(): void
    {
        // MSH.1/MSH.2 carry the delimiters the rest of the message was encoded with.
        $this->assertSame('|', $this->msh->getFieldSeparator()->getValue());
        $this->assertSame('^~\\&', $this->msh->getComponentSeparator()->getValue());
    }

    public function testAddressingFieldsMapToTheirHierarchicDesignators(): void
    {
        $this->assertSame('AccMgr', $this->msh->getSendingApplication()->namespaceId->getValue());
        $this->assertSame('SendFac', $this->msh->getSendingFacility()->namespaceId->getValue());
        $this->assertSame('RecvApp', $this->msh->getReceivingApplication()->namespaceId->getValue());
        $this->assertSame('RecvFac', $this->msh->getReceivingFacility()->namespaceId->getValue());
        $this->assertSame('192.168.1.1', $this->msh->getSendingNetworkAddress()->namespaceId->getValue());
        $this->assertSame('10.0.0.1', $this->msh->getReceivingNetworkAddress()->namespaceId->getValue());
    }

    public function testMessageTypingFieldsDriveRoutingAndGrammarSelection(): void
    {
        // The trigger event selects the message class; the version id selects the grammar.
        $this->assertSame('20050110045504', $this->msh->getDateTimeOfMessage()->getValue());
        $this->assertSame('A01', $this->msh->getMessageType()->triggerEvent->getValue());
        $this->assertSame('2.8', $this->msh->getVersionId()->id->getValue());
    }

    public function testSecurityAndMessageControlIdExposeTheirScalarValues(): void
    {
        // MSH.8 (Security) and MSH.10 (Message Control ID) are opaque strings echoed to the sender.
        $this->assertSame('SECRET', $this->msh->getSecurity()->getValue());
        $this->assertSame('599102', $this->msh->getMessageControlId()->getValue());
    }

    public function testProcessingIdExposesTheProcessingTypeComposite(): void
    {
        // MSH.11 is a Processing Type: id (e.g. P = production) plus processing mode.
        $this->assertSame('P', $this->msh->getProcessingId()->id->getValue());
        $this->assertSame('A', $this->msh->getProcessingId()->mode->getValue());
    }

    public function testCharacterSetCollectsEveryRepetition(): void
    {
        // MSH.18 is repeating, so every declared character set must be retained in order.
        $characterSets = $this->msh->getCharacterSet();

        $this->assertCount(2, $characterSets);
        $this->assertSame('ASCII', $characterSets[0]->getValue());
        $this->assertSame('UNICODE', $characterSets[1]->getValue());
    }

    public function testControlFieldsExposeTheirScalarValues(): void
    {
        $this->assertSame('42', $this->msh->getSequenceNumber()->getValue());
        $this->assertSame('CONT', $this->msh->getContinuationPointer()->getValue());
        $this->assertSame('AL', $this->msh->getAcceptAcknowledgementType()->getValue());
        $this->assertSame('NE', $this->msh->getApplicationAcknowledgementType()->getValue());
        $this->assertSame('USA', $this->msh->getCountryCode()->getValue());
        $this->assertSame('2', $this->msh->getAlternateCharacterSetHandlingScheme()->getValue());
    }

    public function testLanguageAndOrganizationFieldsMapToTheirComposites(): void
    {
        $this->assertSame('en', $this->msh->getPrincipalLanguageOfMessage()->identifier->getValue());
        $this->assertSame('SendOrg', $this->msh->getSendingResponsibleOrganization()->name->getValue());
        $this->assertSame('RecvOrg', $this->msh->getReceivingResponsibleOrganization()->name->getValue());
    }

    public function testMessageProfileIdentifierCollectsEveryRepetition(): void
    {
        // MSH.21 is repeating, so every declared profile must be retained in order.
        $profiles = $this->msh->getMessageProfileIdentifier();

        $this->assertCount(2, $profiles);
        $this->assertSame('PROF1', $profiles[0]->id->getValue());
        $this->assertSame('PROF2', $profiles[1]->id->getValue());
    }
}
