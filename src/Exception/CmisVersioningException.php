<?php
namespace Dkd\PhpCmis\Exception;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * CMIS Versioning Exception.
 *
 * Intent: The operation is attempting to perform an action on a non-current
 * version of a document that cannot be performed on a non-current version.
 */
class CmisVersioningException extends CmisBaseException
{
}
