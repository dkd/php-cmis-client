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
 * Change type Enum.
 */
final class ChangeType extends AbstractEnumeration
{
    const CREATED = 'created';
    const DELETED = 'deleted';
    const SECURITY = 'security';
    const UPDATED = 'updated';
}
