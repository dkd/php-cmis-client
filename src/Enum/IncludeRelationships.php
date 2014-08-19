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
 * Include Relationships Enum.
 */
final class IncludeRelationships extends AbstractEnumeration
{
    const NONE = 'none';
    const SOURCE = 'source';
    const TARGET = 'target';
    const BOTH = 'both';
}
