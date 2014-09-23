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
 * Binding Type Enum.
 */
final class BindingType extends Enumeration
{
    const ATOMPUB = 'atompub';
    const BROWSER = 'browser';
    const CUSTOM = 'custom';
    const WEBSERVICES = 'webservices';
}
