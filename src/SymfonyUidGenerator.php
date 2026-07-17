<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

use Override;
use Symfony\Component\Uid\Uuid;

/**
 * IdGenerator backed by symfony/uid, producing time-ordered UUIDv7 identifiers.
 *
 * Requires the optional symfony/uid package (see the "suggest" section of composer.json).
 */
final class SymfonyUidGenerator implements IdGenerator
{
    #[Override]
    public function generate(): string
    {
        return (string) Uuid::v7();
    }
}
