<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Override;
use RoundingWell\HL7\AbstractPrimitive;
use RoundingWell\HL7\LazyDateTime;

/**
 * Date/Time
 *
 * Represents a YYYY[MM[DD[HH[MM[SS[.S{1,4}]]]]]][+/-ZZZZ] timestamp.
 */
final class DTM extends AbstractPrimitive
{
    private ?LazyDateTime $dt = null;

    private function prepare(): void
    {
        if ($this->dt || $this->getValue() === '') {
            return;
        }

        $this->dt = new LazyDateTime($this->getValue());
    }

    public function getFormat(): ?string
    {
        $this->prepare();

        return $this->dt?->getFormat();
    }

    public function getDateTime(?DateTimeZone $timezone = null): ?DateTimeImmutable
    {
        $this->prepare();

        return $this->dt?->getDateTime($timezone);
    }

    public function setDateTime(DateTimeInterface $value): void
    {
        // Delegate to setValue() so the derived DateTimeImmutable and format stay
        // consistent with a parsed value — one code path builds the internal state.
        $this->setValue($value->format('YmdHisO'));
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
