<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Exception\InvalidFile;
use RoundingWell\HL7\Exception\InvalidMessage;
use RoundingWell\HL7\Message\A01;
use RoundingWell\HL7\Message\A03;
use RoundingWell\HL7\Message\A06;
use RoundingWell\HL7\MessageFactory;

#[CoversClass(MessageFactory::class)]
final class MessageFactoryTest extends TestCase
{
    use CanProvideMessage;

    private MessageFactory $messageFactory;

    #[Override]
    protected function setUp(): void
    {
        $this->messageFactory = new MessageFactory();
    }

    public function testShouldParseFile(): void
    {
        $message = $this->messageFactory->parseFile($this->messagePath('adt-a01'));

        $this->assertInstanceOf(A01::class, $message);
    }

    public function testShouldParseA03File(): void
    {
        $message = $this->messageFactory->parseFile($this->messagePath('adt-a03'));

        $this->assertInstanceOf(A03::class, $message);
    }

    public function testShouldParseA06File(): void
    {
        $message = $this->messageFactory->parseFile($this->messagePath('adt-a06'));

        $this->assertInstanceOf(A06::class, $message);
    }

    public function testParseFileThrowsWhenTheFileDoesNotExist(): void
    {
        // A missing input file is a distinct, actionable failure from a malformed message.
        $this->expectException(InvalidFile::class);
        $this->expectExceptionMessageIsOrContains('HL7 file does not exist');

        $this->messageFactory->parseFile($this->messagePath('does-not-exist'));
    }

    public function testParseFileThrowsWhenTheFileCannotBeRead(): void
    {
        // An existing but unreadable file must surface as a read failure, not "missing".
        $path = tempnam(sys_get_temp_dir(), 'hl7');
        $this->assertIsString($path);
        chmod($path, 0o000);

        if (is_readable($path)) {
            // Permissions are not enforced for this user (e.g. running as root); surface the skip.
            chmod($path, 0o644);
            unlink($path);
            $this->markTestSkipped('Filesystem permissions are not enforced for the current user.');
        }

        // Silence the "Permission denied" warning file_get_contents() raises before returning false.
        set_error_handler(static fn(int $errno, string $errstr): bool => true);

        try {
            $this->expectException(InvalidFile::class);
            $this->expectExceptionMessageIsOrContains('HL7 file cannot be read');

            $this->messageFactory->parseFile($path);
        } finally {
            restore_error_handler();
            chmod($path, 0o644);
            unlink($path);
        }
    }

    public function testParseThrowsWhenTheMessageDoesNotStartWithMsh(): void
    {
        $this->expectException(InvalidMessage::class);
        $this->expectExceptionMessageIsOrContains("must start with 'MSH' segment");

        $this->messageFactory->parse('PID|1||10006579');
    }

    public function testParseThrowsWhenTheMshSegmentHasNoDelimiter(): void
    {
        // Without the delimiter after "MSH" there is nothing to split fields on.
        $this->expectException(InvalidMessage::class);
        $this->expectExceptionMessageIsOrContains('must have a delimiter');

        $this->messageFactory->parse('MSH');
    }

    public function testParseThrowsWhenTheEncodingCharactersAreIncomplete(): void
    {
        // The four encoding characters are required to interpret every downstream field.
        $this->expectException(InvalidMessage::class);
        $this->expectExceptionMessageIsOrContains('must have 4 encoding characters');

        $this->messageFactory->parse('MSH|^~');
    }
}
