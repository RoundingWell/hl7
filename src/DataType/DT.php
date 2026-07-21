<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use DateTimeImmutable;
use DateTimeInterface;
use Override;
use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\LazyDate;

/**
 * Date
 *
 * Represents a YYYY[MM[DD]] date.
 */
final class DT extends AbstractPrimitive
{
    private ?LazyDate $dt = null;

    private function prepare(): void
    {
        if ($this->dt || $this->getValue() === '') {
            return;
        }

        $this->dt = new LazyDate($this->getValue());
    }

    public function getFormat(): ?string
    {
        $this->prepare();

        return $this->dt?->getFormat();
    }

    public function getDateTime(): ?DateTimeImmutable
    {
        $this->prepare();

        return $this->dt?->getDateTime();
    }

    public function setDate(DateTimeInterface $value): void
    {
        // Delegate to setValue() so the derived DateTimeImmutable and format stay
        // consistent with a parsed value — one code path builds the internal state.
        $this->setValue($value->format('Ymd'));
    }

    #[Override]
    public function setValue(string $value): void
    {
        $this->dt = null;

        parent::setValue($value);
    }

    #[Override]
    public function clear(): void
    {
        $this->dt = null;

        parent::clear();
    }
}
