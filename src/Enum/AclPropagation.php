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
 * ACL Propagation Enum.
 */
final class AclPropagation extends Enumeration
{
    const OBJECTONLY = 'objectonly';
    const REPOSITORYDETERMINED = 'repositorydetermined';
    const PROPAGATE = 'propagate';
}
