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
 * ACL Capability Enum: Supported Permissions.
 */
final class SupportedPermissions extends Enumeration
{
    const BASIC = 'basic';
    const BOTH = 'both';
    const REPOSITORY = 'repository';
}
