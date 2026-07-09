<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use RoundingWell\HL7\AbstractSegment;

/**
 * A concrete, no-schema {@see AbstractSegment} used to exercise the abstract base.
 *
 * AbstractSegment derives its name from the class, so tests need a real subclass
 * rather than an anonymous one to get a stable, assertable segment name.
 */
final class FakeSegment extends AbstractSegment {}
