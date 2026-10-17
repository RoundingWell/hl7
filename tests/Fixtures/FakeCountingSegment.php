<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use Override;
use RoundingWell\HL7\AbstractSegment;

/**
 * A schema-less segment that counts how many times it is constructed.
 *
 * Probing a candidate structure for its first names must not build a throwaway segment (SV-76),
 * so a test needs to observe constructions rather than only the parse result.
 */
final class FakeCountingSegment extends AbstractSegment
{
    public static int $instantiations = 0;

    public function __construct()
    {
        self::$instantiations++;
    }

    #[Override]
    public function getName(): string
    {
        return 'ZK1';
    }

    public static function reset(): void
    {
        self::$instantiations = 0;
    }
}
