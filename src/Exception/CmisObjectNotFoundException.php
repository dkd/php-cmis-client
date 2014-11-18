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
 * CMIS ObjectNotFound Exception.
 *
 * Cause: The service call has specified an object, an object-type or a
 * repository that does not exist.
 */
class CmisObjectNotFoundException extends CmisBaseException
{
}
