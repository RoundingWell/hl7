<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RoundingWell\HL7\ExtraComponents;
use RoundingWell\HL7\Varies;

#[CoversClass(ExtraComponents::class)]
final class ExtraComponentsTest extends TestCase
{
    public function testIsInitiallyEmpty(): void
    {
        // A fresh collection must report no components so callers can distinguish
        // "nothing parsed" from a populated collection.
        $extra = new ExtraComponents();

        $this->assertCount(0, $extra);
        $this->assertSame([], $extra->getComponents());
    }

    public function testGetComponentCreatesAVaries(): void
    {
        // Accessing a component that does not yet exist must lazily materialise a
        // Varies rather than fail, so parsing can address components by position.
        $extra = new ExtraComponents();

        $this->assertInstanceOf(Varies::class, $extra->getComponent(0));
    }

    public function testGetComponentReturnsTheSameInstance(): void
    {
        // Repeated access to the same index must yield the same instance, otherwise
        // data written through one reference would be lost on the next access.
        $extra = new ExtraComponents();

        $component = $extra->getComponent(0);

        $this->assertSame($component, $extra->getComponent(0));
    }

    public function testGetComponentBackfillsIntermediateIndexes(): void
    {
        // Requesting a higher index must fill every lower index too, so the
        // collection stays a gapless list addressable from zero.
        $extra = new ExtraComponents();

        $extra->getComponent(2);

        $this->assertCount(3, $extra);

        $components = $extra->getComponents();
        $this->assertContainsOnlyInstancesOf(Varies::class, $components);
        $this->assertNotSame($components[0], $components[1]);
        $this->assertNotSame($components[1], $components[2]);
    }

    public function testClearRemovesAllComponents(): void
    {
        // clear() lets an owning type reset its extras when it is re-parsed, so stale components
        // from an earlier parse cannot leak into the next one.
        $extra = new ExtraComponents();
        $extra->getComponent(1);

        $extra->clear();

        $this->assertCount(0, $extra);
        $this->assertSame([], $extra->getComponents());
    }

    public function testGetComponentsExposesCreatedComponents(): void
    {
        // getComponents must return exactly the components that were materialised,
        // in index order, so the collection can be iterated after parsing.
        $extra = new ExtraComponents();

        $first = $extra->getComponent(0);
        $second = $extra->getComponent(1);

        $this->assertSame([$first, $second], $extra->getComponents());
    }
}
