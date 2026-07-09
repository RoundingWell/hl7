<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Countable;
use Override;

/**
 * Extra components attached to a field
 *
 * These are components that are not part of the standard HL7 field definition,
 * but are included in the field data for compatibility with other systems that
 * may set extended values on fields.
 */
final class ExtraComponents implements Countable
{
    use CanAssertNumbers;

    /** @var list<Varies> */
    private array $components = [];

    #[Override]
    public function count(): int
    {
        return count($this->components);
    }

    /**
     * @return list<Varies>
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    public function getComponent(int $number): Varies
    {
        $this->assertNaturalNumber($number);

        for ($i = count($this->components); $i <= $number; $i++) {
            $this->components[$i] ??= new Varies();
        }

        // Mago does not understand that the previous loop ensures the index is defined...
        // @mago-expect analysis:possibly-undefined-int-array-index
        // @mago-expect analysis:nullable-return-statement
        // @mago-expect analysis:invalid-return-statement
        return $this->components[$number];
    }
}
