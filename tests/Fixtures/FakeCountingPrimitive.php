<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use RoundingWell\HL7\AbstractPrimitive;

/**
 * A primitive that counts how many times it is constructed.
 *
 * Parsing a segment must not build any field until one is read (SV-77), so a test needs to
 * observe constructions rather than only the values that come back.
 */
final class FakeCountingPrimitive extends AbstractPrimitive
{
    public static int $instantiations = 0;

    public function __construct()
    {
        self::$instantiations++;

        parent::__construct();
    }

    public static function reset(): void
    {
        self::$instantiations = 0;
    }
}
