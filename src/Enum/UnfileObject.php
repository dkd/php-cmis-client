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
 * Unfile Object Enum.
 */
final class UnfileObject extends Enumeration
{
    const DELETE = 'delete';
    const DELETESINGLEFILED = 'deletesinglefiled';
    const UNFILE = 'unfile';
}
