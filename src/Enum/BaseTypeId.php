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
 * Base Object Type IDs Enum.
 */
final class BaseTypeId extends Enumeration
{
    const CMIS_DOCUMENT = 'cmis:document';
    const CMIS_FOLDER = 'cmis:folder';
    const CMIS_ITEM = 'cmis:item';
    const CMIS_POLICY = 'cmis:policy';
    const CMIS_RELATIONSHIP = 'cmis:relationship';
    const CMIS_SECONDARY = 'cmis:secondary';
}
