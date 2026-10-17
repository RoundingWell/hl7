<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Exception;
use Override;

final class Varies implements Variable, TracksMutation
{
    use CanTrackMutation;

    private Type $data;

    public function __construct()
    {
        $this->data = new GenericPrimitive();
    }

    #[Override]
    public function track(DirtyFlag $dirty): void
    {
        $this->dirty = $dirty;

        if ($this->data instanceof TracksMutation) {
            $this->data->track($dirty);
        }
    }

    #[Override]
    public function setField(string $name): void
    {
        $this->data->setField($name);
    }

    #[Override]
    public function getField(): string
    {
        return $this->data->getField();
    }

    #[Override]
    public function getName(): string
    {
        return $this->getData()->getName();
    }

    #[Override]
    public function clear(): void
    {
        $this->getData()->clear();
    }

    #[Override]
    public function getExtraComponents(): ExtraComponents
    {
        return $this->getData()->getExtraComponents();
    }

    #[Override]
    public function parse(Encoding $encoding, string $data): void
    {
        $this->getData()->parse($encoding, $data);
    }

    #[Override]
    public function serialize(Encoding $encoding): string
    {
        return $this->getData()->serialize($encoding);
    }

    #[Override]
    public function getData(): Type
    {
        return $this->data;
    }

    #[Override]
    public function setData(Type $data): void
    {
        if ($this->data instanceof Primitive && $data instanceof Primitive) {
            // Copy first so a rejected value throws before the segment line is dropped.
            $data->setValue($this->data->getValue());
            $this->data = $data;

            if ($this->dirty !== null && $data instanceof TracksMutation) {
                $data->track($this->dirty);
            }

            $this->markDirty();

            return;
        }

        throw new Exception(
            '(missing implementation) Cannot set data on ' . $this->data::class . ' with data of type ' . $data::class,
        );

        // Util::deepCopy($this->data, $data);
    }
}
