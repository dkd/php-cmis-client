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
 * CMIS NameConstraintViolation Exception.
 *
 * Intent: The repository is not able to store the object that the user is
 * creating/updating due to a name constraint violation.
 */
class CmisNameConstraintViolationException extends CmisBaseException
{
}
