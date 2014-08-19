<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Capability Enum: ACL.
 */
final class CapabilityAcl extends AbstractEnumeration
{
    const DISCOVER = 'discover';
    const MANAGE = 'manage';
    const NONE = 'none';
}
