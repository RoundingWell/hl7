<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\DataType;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\DataType\JCC;
use RoundingWell\HL7\Encoding;

#[CoversClass(JCC::class)]
final class JCCTest extends TestCase
{
    public function testComponentsMapJobCodeClassAndDescription(): void
    {
        // A job code/class pairs a coded job code and class with free-text description.
        $jcc = new JCC();
        $jcc->parse(new Encoding(), 'RN^EXEMPT^Registered Nurse');

        $this->assertSame('RN', $jcc->getJobCode()->getIdentifier()->getValue());
        $this->assertSame('EXEMPT', $jcc->getJobClass()->getIdentifier()->getValue());
        $this->assertSame('Registered Nurse', $jcc->getJobDescriptionText()->getValue());
    }
}
