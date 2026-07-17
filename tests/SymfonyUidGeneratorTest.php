<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\SymfonyUidGenerator;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(SymfonyUidGenerator::class)]
final class SymfonyUidGeneratorTest extends TestCase
{
    public function testGeneratesAValidUuidV7String(): void
    {
        // MSH-10 must be a unique, well-formed control ID; UUIDv7 gives time-ordered
        // uniqueness. The value must be a valid, correctly-versioned UUID string.
        $id = new SymfonyUidGenerator()->generate();

        $this->assertTrue(Uuid::isValid($id));
        $this->assertInstanceOf(UuidV7::class, Uuid::fromString($id));
    }

    public function testGeneratesADistinctValueEachCall(): void
    {
        // Each acknowledgment is its own message and must not reuse a control ID.
        $generator = new SymfonyUidGenerator();

        $this->assertNotSame($generator->generate(), $generator->generate());
    }
}
