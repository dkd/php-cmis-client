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
 * CMIS NotSupported Exception.
 *
 * Cause: The service method invoked requires an optional capability not
 * supported by the repository.
 */
class CmisNotSupportedException extends CmisBaseException
{
}
