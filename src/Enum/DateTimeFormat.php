<?php
namespace Dkd\PhpCmis\Enum;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\Enumeration\Enumeration;

/**
 * Date Time Format Enum.
 */
final class DateTimeFormat extends Enumeration
{
    const __DEFAULT = self::SIMPLE;
    const SIMPLE = 'simple';
    const EXTENDED = 'extended';
}
