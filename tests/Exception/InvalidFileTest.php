<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Exception;

use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\HL7Exception;
use RoundingWell\HL7\Exception\InvalidFile;
use RuntimeException;

final class InvalidFileTest extends TestCase
{
    public function testItIsAnHl7Exception(): void
    {
        // Callers catch HL7Exception to handle any parser failure uniformly.
        $this->assertInstanceOf(HL7Exception::class, InvalidFile::doesNotExist('/tmp/x.hl7'));
    }

    public function testItIsARuntimeException(): void
    {
        // Filesystem availability is a runtime condition, not a bad caller argument.
        $this->assertInstanceOf(RuntimeException::class, InvalidFile::doesNotExist('/tmp/x.hl7'));
    }

    public function testDoesNotExistNamesTheMissingPath(): void
    {
        // The path must appear so the caller can tell which file was missing.
        $this->assertSame(
            'HL7 file does not exist: /tmp/missing.hl7',
            InvalidFile::doesNotExist('/tmp/missing.hl7')->getMessage(),
        );
    }

    public function testCannotReadNamesTheUnreadablePath(): void
    {
        $this->assertSame(
            'HL7 file cannot be read: /tmp/locked.hl7',
            InvalidFile::cannotRead('/tmp/locked.hl7')->getMessage(),
        );
    }
}
