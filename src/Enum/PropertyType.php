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
 * Property Type Enum.
 */
final class PropertyType extends AbstractEnumeration
{
    const BOOLEAN = 'boolean';
    const DATETIME = 'datetime';
    const DECIMAL = 'decimal';
    const HTML = 'html';
    const ID = 'id';
    const INTEGER = 'integer';
    const STRING = 'string';
    const URI = 'uri';
}
