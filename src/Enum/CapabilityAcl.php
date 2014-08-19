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
