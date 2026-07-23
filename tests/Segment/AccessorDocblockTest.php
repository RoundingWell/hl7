<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Segment;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use RoundingWell\HL7\AbstractSegment;

/**
 * Guards the field-reference docblocks on every named segment accessor.
 *
 * Each accessor documents the HL7 field it reads, e.g. "PID.7 Date/Time of Birth".
 * The reference must stay accurate: the number must match the field the accessor
 * actually reads, and the name must match that field's definition. Discovery is by
 * filesystem so a newly added segment is covered automatically, without an edit here.
 */
#[CoversNothing]
final class AccessorDocblockTest extends TestCase
{
    /**
     * Every named segment must be discovered, so the sweep can never pass silently
     * by scanning nothing. This list mirrors the typed segments in SegmentFactory.
     */
    public function testDiscoversEveryNamedSegment(): void
    {
        $discovered = [];
        foreach (array_keys(self::accessorProvider()) as $case) {
            $discovered[strstr($case, '::', true)] = true;
        }

        $this->assertSame(
            ['DG1', 'DRG', 'EVN', 'MSA', 'MSH', 'NK1', 'OBX', 'PID', 'PV1', 'PV2'],
            array_keys($discovered),
        );
    }

    #[DataProvider('accessorProvider')]
    public function testAccessorDocblockReferencesItsField(string $class, string $method): void
    {
        $segment = new $class();
        $this->assertInstanceOf(AbstractSegment::class, $segment);

        $reflection = new ReflectionMethod($class, $method);

        // The field the accessor actually reads, taken from its body — not the docblock —
        // so a docblock that drifts from the real field number is caught.
        $number = self::fieldNumberRead($reflection);

        $names = $segment->getNames();
        $this->assertArrayHasKey($number - 1, $names, "{$class}::{$method} reads an undefined field {$number}");

        $expected = sprintf('%s.%d %s', $segment->getName(), $number, $names[$number - 1]);

        $summary = self::docblockSummary($reflection);
        $this->assertNotNull($summary, "{$class}::{$method} is missing a field-reference docblock");
        $this->assertSame($expected, $summary, "{$class}::{$method} docblock does not match its field");
    }

    /**
     * Discovers every public accessor (get*) declared directly on a named segment class.
     *
     * @return array<string, array{class-string<AbstractSegment>, string}>
     */
    public static function accessorProvider(): array
    {
        $cases = [];

        $files = glob(__DIR__ . '/../../src/Segment/*.php');
        if ($files === false) {
            return $cases;
        }

        foreach ($files as $file) {
            $short = basename($file, '.php');
            /** @var class-string<AbstractSegment> $class */
            $class = 'RoundingWell\\HL7\\Segment\\' . $short;

            foreach (new ReflectionClass($class)->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                // Skip inherited helpers (getName, getField, ...) and non-accessors (parse, serialize).
                if ($method->getDeclaringClass()->getName() !== $class || !str_starts_with($method->getName(), 'get')) {
                    continue;
                }

                $cases["{$short}::{$method->getName()}"] = [$class, $method->getName()];
            }
        }

        return $cases;
    }

    private static function fieldNumberRead(ReflectionMethod $method): int
    {
        $lines = file((string) $method->getFileName());
        if ($lines === false) {
            self::fail("Could not read the source of {$method->getName()}");
        }

        $body = implode('', array_slice(
            $lines,
            $method->getStartLine() - 1,
            $method->getEndLine() - $method->getStartLine() + 1,
        ));

        if (preg_match('/getField(?:Repetition)?\(\s*(\d+)/', $body, $matches) !== 1) {
            self::fail("Could not determine the field read by {$method->getName()}");
        }

        return (int) $matches[1];
    }

    private static function docblockSummary(ReflectionMethod $method): ?string
    {
        $doc = $method->getDocComment();
        if ($doc === false) {
            return null;
        }

        foreach (explode("\n", $doc) as $line) {
            $line = trim(trim($line), '/*');
            $line = trim($line);
            if ($line !== '') {
                return $line;
            }
        }

        return null;
    }
}
