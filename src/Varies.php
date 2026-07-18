<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Exception;
use Override;

final class Varies implements Variable
{
    private Type $data;

    public function __construct()
    {
        $this->data = new GenericPrimitive();
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
            $data->setValue($this->data->getValue());
            $this->data = $data;

            return;
        }

        throw new Exception(
            '(missing implementation) Cannot set data on ' . $this->data::class . ' with data of type ' . $data::class,
        );

        // Util::deepCopy($this->data, $data);
    }
}
