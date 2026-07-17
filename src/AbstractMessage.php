<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Override;
use Psr\Clock\ClockInterface;
use RoundingWell\HL7\Message\ACK;
use RoundingWell\HL7\Segment\MSH;

abstract class AbstractMessage extends AbstractGroup implements Message
{
    #[Override]
    public function getVersion(): string
    {
        return $this->getMSH()->getVersionId()->getId()->getValue();
    }

    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        $segments = [];

        foreach (array_filter(explode($encoding->lineEnding, $data)) as $line) {
            [$name] = explode($encoding->fieldSeparator, $line, 2);

            $segments[] = new SegmentElement($name, $line);
        }

        $this->parseStructures(new SegmentCursor(...$segments), $encoding);
    }

    public function getSegment(string $name, int $repetition): Segment
    {
        return $this->getRepetition($name, $repetition);
    }

    #[Override]
    public function getMSH(): MSH
    {
        return $this->getSegment('MSH', 0);
    }

    public function generateACK(AcknowledgmentCode $code, ClockInterface $clock, IdGenerator $idGenerator): Message
    {
        $ack = new ACK();

        $this->fillResponseHeader($ack, $clock, $idGenerator);

        // MSA-1 carries the acknowledgment result; MSA-2 echoes the inbound control ID so the
        // original sender can correlate this acknowledgment with the message it sent.
        $ack->getMSA()->getAcknowledgmentCode()->setValue($code->value);
        $ack->getMSA()->getMessageControlId()->setValue($this->getMSH()->getMessageControlId()->getValue());

        return $ack;
    }

    private function fillResponseHeader(Message $out, ClockInterface $clock, IdGenerator $idGenerator): void
    {
        $in = $this->getMSH();
        $msh = $out->getMSH();

        $this->copyType($in->getFieldSeparator(), $msh->getFieldSeparator());
        $this->copyType($in->getEncodingCharacters(), $msh->getEncodingCharacters());

        // Swap sender and receiver so the acknowledgment is addressed back to the origin.
        $this->copyType($in->getReceivingApplication(), $msh->getSendingApplication());
        $this->copyType($in->getReceivingFacility(), $msh->getSendingFacility());
        $this->copyType($in->getSendingApplication(), $msh->getReceivingApplication());
        $this->copyType($in->getSendingFacility(), $msh->getReceivingFacility());

        $msh->getDateTimeOfMessage()->setDateTime($clock->now());

        $msh->getMessageType()->getMessageType()->setValue('ACK');
        $msh->getMessageType()->getTriggerEvent()->setValue($in->getMessageType()->getTriggerEvent()->getValue());
        $msh->getMessageType()->getMessageStructure()->setValue('ACK');

        // The acknowledgment is its own message, so it gets a fresh control ID.
        $msh->getMessageControlId()->setValue($idGenerator->generate());

        $this->copyType($in->getProcessingId(), $msh->getProcessingId());
        $this->copyType($in->getVersionId(), $msh->getVersionId());
    }

    /**
     * Copies a value from one field to another of the SAME concrete type.
     *
     * Callers must pass matching types (both Primitive, or both Composite with
     * identical component definitions); mismatched types are silently ignored.
     */
    private function copyType(Type $from, Type $to): void
    {
        if ($from instanceof Primitive && $to instanceof Primitive) {
            $to->setValue($from->getValue());

            return;
        }

        if ($from instanceof Composite && $to instanceof Composite) {
            $toComponents = $to->getComponents();

            foreach ($from->getComponents() as $index => $component) {
                // @mago-expect analysis:possibly-undefined-int-array-index
                $toComponent = $toComponents[$index];

                assert($toComponent instanceof Type, 'Matched component index must exist in both composites');

                $this->copyType($component, $toComponent);
            }
        }
    }
}
