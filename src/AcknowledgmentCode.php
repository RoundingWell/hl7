<?php

declare(strict_types=1);

namespace RoundingWell\HL7;

/**
 * Acknowledgment Code (HL7 table 0008)
 */
enum AcknowledgmentCode: string
{
    /** Application Accept */
    case AA = 'AA';

    /** Application Error */
    case AE = 'AE';

    /** Application Reject */
    case AR = 'AR';
}
