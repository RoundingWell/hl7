<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests\Fixtures;

use RoundingWell\HL7\AbstractComposite;
use RoundingWell\HL7\DataType\ST;
use RoundingWell\HL7\TypeDefinition;

/**
 * A composite whose first component is itself a {@see FakeComposite}, used to exercise the
 * depth-aware separator rule: a nested composite splits on the subcomponent separator, while the
 * outer composite still splits on the component separator.
 */
final class FakeNestedComposite extends AbstractComposite
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new TypeDefinition('Inner', FakeComposite::class));
        $this->add(new TypeDefinition('Trailer', ST::class));
    }
}
