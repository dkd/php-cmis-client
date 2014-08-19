<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * ACL Capability Enum: Supported Permissions.
 */
final class SupportedPermissions extends AbstractEnumeration
{
    const BASIC = 'basic';
    const BOTH = 'both';
    const REPOSITORY = 'repository';
}
