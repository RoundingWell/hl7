<?php

declare(strict_types=1);

namespace RoundingWell\HL7\DataType;

/**
 * Job Code/Class
 */
final readonly class JCC extends Composite
{
    public function __construct(
        public CWE $jobCode = new CWE(),
        public CWE $jobClass = new CWE(),
        public TX $jobDescriptionText = new TX(),
    ) {}
}
