<?php
namespace Dkd\PhpCmis\Enum;

/*
 * This file is part of php-cmis-client.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\Enumeration\Enumeration;

/**
 * Relationship Direction Enum.
 */
final class RelationshipDirection extends Enumeration
{
    const EITHER = 'either';
    const SOURCE = 'source';
    const TARGET = 'target';
}
