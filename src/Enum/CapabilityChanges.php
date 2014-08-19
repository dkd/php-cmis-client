<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Capability Enum: Changes.
 */
final class CapabilityChanges extends AbstractEnumeration
{
    const ALL = 'all';
    const NONE = 'none';
    const OBJECTIDSONLY = 'objectidsonly';
    const PROPERTIES = 'properties';
}
