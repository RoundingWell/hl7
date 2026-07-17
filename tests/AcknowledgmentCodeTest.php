<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\AcknowledgmentCode;

#[CoversClass(AcknowledgmentCode::class)]
final class AcknowledgmentCodeTest extends TestCase
{
    public function testBackingValuesAreTheHl7Table0008Codes(): void
    {
        // These backing values are written verbatim into MSA-1; they must be the exact
        // HL7 table 0008 codes a receiving system expects, not renamed constants.
        $this->assertSame('AA', AcknowledgmentCode::AA->value);
        $this->assertSame('AE', AcknowledgmentCode::AE->value);
        $this->assertSame('AR', AcknowledgmentCode::AR->value);
    }
}
