<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use OutOfBoundsException;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\Encoding;
use RoundingWell\HL7\Exception\InvalidFile;
use RoundingWell\HL7\Exception\InvalidMessage;
use RoundingWell\HL7\GenericMessage;
use RoundingWell\HL7\Message\ADT\A01;
use RoundingWell\HL7\Message\ADT\A03;
use RoundingWell\HL7\Message\ADT\A04;
use RoundingWell\HL7\Message\ADT\A06;
use RoundingWell\HL7\Message\ADT\A07;
use RoundingWell\HL7\Message\ADT\A08;
use RoundingWell\HL7\Message\ADT\A13;
use RoundingWell\HL7\MessageFactory;
use RoundingWell\HL7\Segment\PID;

use function array_map;
use function chmod;
use function explode;
use function implode;
use function is_readable;
use function restore_error_handler;
use function rtrim;
use function set_error_handler;
use function str_replace;
use function str_starts_with;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

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

    public function testShouldParseA04File(): void
    {
        $message = $this->messageFactory->parseFile($this->messagePath('adt-a04'));

        $this->assertInstanceOf(A04::class, $message);
    }

    public function testShouldParseA06File(): void
    {
        $message = $this->messageFactory->parseFile($this->messagePath('adt-a06'));

        $this->assertInstanceOf(A06::class, $message);
    }

    public function testShouldParseA07File(): void
    {
        $message = $this->messageFactory->parseFile($this->messagePath('adt-a07'));

        $this->assertInstanceOf(A07::class, $message);
    }

    public function testShouldParseA08File(): void
    {
        $message = $this->messageFactory->parseFile($this->messagePath('adt-a08'));

        $this->assertInstanceOf(A08::class, $message);
    }

    public function testShouldParseA13File(): void
    {
        $message = $this->messageFactory->parseFile($this->messagePath('adt-a13'));

        $this->assertInstanceOf(A13::class, $message);
    }

    public function testShouldParseAdditionalEncodingCharacters(): void
    {
        $message = $this->messageFactory->parse('MSH|^~\&#|AccMrg\rPID|1');

        $this->assertSame('^~\&#', $message->getMSH()->getEncodingCharacters()->getValue());
    }

    public function testParseDefersAMalformedFieldUntilItIsRead(): void
    {
        // PID.1 allows a single repetition; the over-repeated value must not fail the message or
        // a read of another PID field, only a read of PID.1 itself.
        $message = $this->messageFactory->parse(
            "MSH|^~\\&|App|Fac|||20050110045504||ADT^A01|1|P|2.3\rEVN|A01\rPID|1~2~3||X||DUCK^DONALD",
        );
        $pid = $message->getSegment('PID', 0);

        $this->assertInstanceOf(PID::class, $pid);
        $this->assertSame('DUCK', $pid->getPatientName()[0]->getFamilyName()->getSurname()->getValue());

        // The type, not the message: the maxReps message is changing under SV-83.
        $this->expectException(OutOfBoundsException::class);

        $pid->getIdentity();
    }

    public function testSerializeOfAnUnreadMalformedFieldIsByteIdentical(): void
    {
        // A malformed field that failed its read is still forwarded exactly as received.
        $data = "MSH|^~\\&|App|Fac|||20050110045504||ADT^A01|1|P|2.3\rEVN|A01\rPID|1~2~3||X||DUCK^DONALD";
        $message = $this->messageFactory->parse($data);
        $pid = $message->getSegment('PID', 0);
        $this->assertInstanceOf(PID::class, $pid);
        $pid->getPatientName();

        try {
            $pid->getIdentity();
            $this->fail('Reading an over-repeated PID.1 must throw');
        } catch (OutOfBoundsException $e) {
            // PID.1 stays unparsed.
            $this->assertStringContainsString('PID.1', $e->getMessage());
        }

        $this->assertSame($data, $message->serialize(new Encoding()));
    }

    #[DataProvider('fixtures')]
    public function testParseThenSerializeIsByteIdenticalForEveryFixture(string $name, string $lineEnding): void
    {
        // Serializing with the default encoding joins lines with CR, whatever the input used, and
        // every line must come back byte for byte, before and after reading every field.
        $data = $this->messageData($name);
        $expected = str_replace(search: "\n", replace: "\r", subject: rtrim($data, characters: "\n"));
        $message = $this->messageFactory->parse(str_replace(search: "\n", replace: $lineEnding, subject: $data));

        $this->assertSame($expected, $message->serialize(new Encoding()));

        // debug() reads every field of every segment.
        $message->debug();

        $this->assertSame($expected, $message->serialize(new Encoding()));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function fixtures(): iterable
    {
        foreach (['adt-a01', 'adt-a01-2', 'adt-a03', 'adt-a04', 'adt-a06', 'adt-a07', 'adt-a08', 'adt-a13'] as $name) {
            yield "{$name} LF" => [$name, "\n"];
            yield "{$name} CRLF" => [$name, "\r\n"];
        }
    }

    public function testWritingOneFieldChangesOnlyThatField(): void
    {
        // Renaming the patient rewrites PID.5's family name and nothing else in the message.
        $data = rtrim($this->messageData('adt-a01'), characters: "\n");
        $message = $this->messageFactory->parse($data);
        $pid = $message->getSegment('PID', 0);
        $this->assertInstanceOf(PID::class, $pid);
        $pid->getPatientName()[0]->getFamilyName()->getSurname()->setValue('MOUSE');

        // The same name also appears in GT1 and IN1; only the PID line may change.
        $expected = array_map(
            static fn(string $line): string => str_starts_with($line, 'PID|')
                ? str_replace(search: '|DUCK^DONALD^D|', replace: '|MOUSE^DONALD^D|', subject: $line)
                : $line,
            explode(separator: "\n", string: $data),
        );

        $this->assertSame(implode(separator: "\r", array: $expected), $message->serialize(new Encoding()));
    }

    public function testParseDefersAMalformedHeaderFieldOtherThanTypeAndVersion(): void
    {
        // Only MSH.9 and MSH.12 decide the message class, so a malformed MSH.13 (Sequence Number,
        // NM) must not fail parsing.
        $message = $this->messageFactory->parse("MSH|^~\\&|App|Fac|||20050110045504||ADT^A01|1|P|2.3|abc\rEVN|A01");

        $this->assertInstanceOf(A01::class, $message);
        $this->assertSame('2.3', $message->getVersion());
    }

    public function testUnknownEventFallsBackToGenericMessage(): void
    {
        // Non-ADT / unknown events must still parse into a usable Message, not fail.
        $message = $this->messageFactory->parse("MSH|^~\\&|App|Fac|||202601011200||ORU^R01|1|P|2.8.1\rOBX|1");

        $this->assertInstanceOf(GenericMessage::class, $message);
    }

    public function testParseFileThrowsWhenTheFileDoesNotExist(): void
    {
        // A missing input file is a distinct, actionable failure from a malformed message.
        $this->expectException(InvalidFile::class);
        $this->expectExceptionMessage('HL7 file does not exist');

        $this->messageFactory->parseFile($this->messagePath('does-not-exist'));
    }

    public function testParseFileThrowsWhenTheFileCannotBeRead(): void
    {
        // An existing but unreadable file must surface as a read failure, not "missing".
        $path = tempnam(sys_get_temp_dir(), prefix: 'hl7');
        $this->assertIsString($path);
        chmod($path, permissions: 0o000);

        if (is_readable($path)) {
            // Permissions are not enforced for this user (e.g. running as root); surface the skip.
            chmod($path, permissions: 0o644);
            unlink($path);
            $this->markTestSkipped('Filesystem permissions are not enforced for the current user.');
        }

        // Silence the "Permission denied" warning file_get_contents() raises before returning false.
        set_error_handler(static fn(int $errno, string $errstr): bool => true);

        try {
            $this->expectException(InvalidFile::class);
            $this->expectExceptionMessage('HL7 file cannot be read');

            $this->messageFactory->parseFile($path);
        } finally {
            restore_error_handler();
            chmod($path, permissions: 0o644);
            unlink($path);
        }
    }

    public function testParseThrowsWhenTheMessageDoesNotStartWithMsh(): void
    {
        $this->expectException(InvalidMessage::class);
        $this->expectExceptionMessage("must start with 'MSH' segment");

        $this->messageFactory->parse('PID|1||10006579');
    }

    public function testParseThrowsWhenTheMshSegmentHasNoDelimiter(): void
    {
        // Without the delimiter after "MSH" there is nothing to split fields on.
        $this->expectException(InvalidMessage::class);
        $this->expectExceptionMessage('must have a field separator');

        $this->messageFactory->parse('MSH');
    }

    public function testParseThrowsWhenTheEncodingCharactersAreIncomplete(): void
    {
        // The four encoding characters are required to interpret every downstream field.
        $this->expectException(InvalidMessage::class);
        $this->expectExceptionMessage('must have 4+ encoding characters');

        $this->messageFactory->parse('MSH|^~');
    }
}
