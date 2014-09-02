<?php
namespace Dkd\PhpCmis\Enum;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\Enumeration\Enumeration;

/**
 * Extension Level Enum
 */
final class ExtensionLevel extends Enumeration
{
    const ACL = 'acl';
    const ALLOWABLE_ACTIONS = 'allowableActions';
    const CHANGE_EVENT = 'changeEvent';
    const OBJECT = 'object';
    const POLICIES = 'policies';
    const PROPERTIES = 'properties';
}
