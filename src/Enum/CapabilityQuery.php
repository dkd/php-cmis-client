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
 * Capability Enum: Query.
 */
final class CapabilityQuery extends Enumeration
{
    const __DEFAULT = self::NONE;
    const BOTHCOMBINED = 'bothcombined';
    const BOTHSEPARATE = 'bothseparate';
    const FULLTEXTONLY = 'fulltextonly';
    const METADATAONLY = 'metadataonly';
    const NONE = 'none';
}
