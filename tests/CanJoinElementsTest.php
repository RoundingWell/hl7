<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\CanJoinElements;
use RoundingWell\HL7\Tests\Fixtures\FakeJoiner;

#[CoversTrait(CanJoinElements::class)]
final class CanJoinElementsTest extends TestCase
{
    public function testJoinsPartsWithTheSeparator(): void
    {
        // The base behaviour is a plain join; trimming only affects trailing empties.
        $this->assertSame('a&b&c', new FakeJoiner()->join(['a', 'b', 'c'], '&'));
    }

    public function testDropsTheTrailingRunOfEmptyParts(): void
    {
        // Trailing empty parts carry no HL7 information, so canonical output omits them.
        $this->assertSame('a&b', new FakeJoiner()->join(['a', 'b', '', ''], '&'));
    }

    public function testKeepsInteriorEmptyParts(): void
    {
        // An empty part is only dropped when nothing non-empty follows it: interior gaps are
        // positional and must survive, or later parts would shift position.
        $this->assertSame('a&&c', new FakeJoiner()->join(['a', '', 'c'], '&'));
    }

    public function testAllEmptyPartsCollapseToAnEmptyString(): void
    {
        // A wholly empty element serializes to "", which the next level up will itself trim.
        $this->assertSame('', new FakeJoiner()->join(['', ''], '&'));
    }

    public function testEmptyListIsAnEmptyString(): void
    {
        $this->assertSame('', new FakeJoiner()->join([], '&'));
    }
}
