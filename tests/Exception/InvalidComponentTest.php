<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Exception;

use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\HL7Exception;
use RoundingWell\HL7\Exception\InvalidComponent;

#[CoversClass(InvalidComponent::class)]
final class InvalidComponentTest extends TestCase
{
    public function testItIsAnHl7Exception(): void
    {
        // Callers catch HL7Exception to handle any parser failure uniformly.
        $this->assertInstanceOf(HL7Exception::class, InvalidComponent::notDefined('MSG', 3));
    }

    public function testItIsAnOutOfBoundsException(): void
    {
        // Addressing a component index the type does not declare is an out-of-range access.
        $this->assertInstanceOf(OutOfBoundsException::class, InvalidComponent::notDefined('MSG', 3));
    }

    public function testNotDefinedNamesTheTypeAndComponentNumber(): void
    {
        // The type and 1-based component number must appear so the caller can locate the bad component.
        $this->assertSame("Component 'MSG.3' is not defined", InvalidComponent::notDefined('MSG', 3)->getMessage());
    }
}
