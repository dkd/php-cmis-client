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
 * CMIS PermissionDenied Exception.
 *
 * Cause: The caller of the service method does not have sufficient permissions
 * to perform the operation.
 */
class CmisPermissionDeniedException extends CmisBaseException
{
}
