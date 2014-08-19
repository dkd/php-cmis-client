<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Capability Order by enum
 */
final class CapabilityOrderBy extends AbstractEnumeration
{
    const COMMON = 'common';
    const CUSTOM = 'custom';
    const NONE = 'none';
}
