<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Decimal Precision Enum.
 */
final class DecimalPrecision extends AbstractEnumeration
{
    const BITS32 = '32';
    const BITS64 = '64';
}
