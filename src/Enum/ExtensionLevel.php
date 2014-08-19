<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Extension Level Enum
 */
final class ExtensionLevel extends AbstractEnumeration
{
    const ACL = 'acl';
    const ALLOWABLE_ACTIONS = 'allowableActions';
    const CHANGE_EVENT = 'changeEvent';
    const OBJECT = 'object';
    const POLICIES = 'policies';
    const PROPERTIES = 'properties';
}
