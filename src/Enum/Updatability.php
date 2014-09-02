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
 * Updatability Enum.
 */
final class Updatability extends Enumeration
{
    const ONCREATE = 'oncreate';
    const READONLY = 'readonly';
    const READWRITE = 'readwrite';
    const WHENCHECKEDOUT = 'whencheckedout';
}
