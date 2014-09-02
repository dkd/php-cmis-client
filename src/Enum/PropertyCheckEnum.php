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
 * Property Check Enum.
 */
final class PropertyCheckEnum extends Enumeration
{
    const MUST_BE_SET = 'MUST_BE_SET';
    const MUST_NOT_BE_SET = 'MUST_NOT_BE_SET';
    const NO_VALUE_CHECK = 'NO_VALUE_CHECK';
    const STRING_MUST_NOT_BE_EMPTY = 'STRING_MUST_NOT_BE_EMPTY';
    const STRING_SHOULD_NOT_BE_EMPTY = 'STRING_SHOULD_NOT_BE_EMPTY';
}
