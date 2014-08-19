<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Cardinality Enum
 */
final class Cardinality extends AbstractEnumeration
{
    const MULTI  = 'multi';
    const SINGLE = 'single';
}
