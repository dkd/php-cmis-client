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
 * Binding Type Enum.
 */
final class BindingType extends AbstractEnumeration
{
    const ATOMPUB = 'atompub';
    const BROWSER = 'browser';
    const CUSTOM = 'custom';
    const LOCAL = 'local';
    const WEBSERVICES = 'webservices';
}
