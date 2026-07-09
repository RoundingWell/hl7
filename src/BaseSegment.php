<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use RoundingWell\HL7\DataType\Generic;
use RoundingWell\HL7\Exception\InvalidField;

class BaseSegment
{
    private readonly string $id;

    /** @var array<int, BaseField> */
    private array $fields = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    final public function getId(): string
    {
        return $this->id;
    }

    /**
     * @throws InvalidField if the field is not defined.
     */
    final public function getField(int $number): BaseField
    {
        // @mago-expect lint:no-isset
        if (!isset($this->fields[$number])) {
            return $this->createGenericField($number);
        }

        // @mago-expect analysis:nullable-return-statement,possibly-undefined-int-array-index,invalid-return-statement
        return $this->fields[$number];
    }

    /**
     * @throws InvalidField if the field number lower than 1.
     */
    final public function addField(int $number, BaseField $field): void
    {
        if ($number < 1) {
            throw InvalidField::tooLow($this->getId(), $number);
        }

        $this->fields[$number] = $field;
    }

    /**
     * @param list<string> $values
     */
    final public function setRaw(Encoding $encoding, array $values): void
    {
        foreach ($values as $idx => $value) {
            $this->getField($idx + 1)->setRaw($encoding, $value);
        }
    }

    private function createGenericField(int $number): BaseField
    {
        // Unknown fields are created as Generic type fields, allowing any value to be set.
        $field = new BaseField('Unknown', Generic::class);

        $this->addField($number, $field);

        return $field;
    }
}
