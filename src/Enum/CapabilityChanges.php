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
 * Capability Enum: Changes.
 */
final class CapabilityChanges extends AbstractEnumeration
{
    const ALL = 'all';
    const NONE = 'none';
    const OBJECTIDSONLY = 'objectidsonly';
    const PROPERTIES = 'properties';
}
