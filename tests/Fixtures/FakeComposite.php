<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\TypeDefinition;

/**
 * A concrete two-component {@see AbstractComposite} used to exercise the abstract base
 * directly (naming, component access, and the extra-components overflow path).
 */
final class FakeComposite extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('First', ST::class));
        $this->add(new TypeDefinition('Second', ST::class));
    }
}
