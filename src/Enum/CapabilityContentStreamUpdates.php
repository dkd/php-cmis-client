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
 * Capability Enum: Content Stream Updates.
 */
final class CapabilityContentStreamUpdates extends Enumeration
{
    const __DEFAULT = self::NONE;
    const ANYTIME = 'anytime';
    const NONE = 'none';
    const PWCONLY = 'pwconly';
}
