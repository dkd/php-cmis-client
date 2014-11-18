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
 * CMIS Storage Exception.
 *
 * Intent: The repository is not able to store the object that the user is
 * creating/updating due to an internal storage problem.
 */
class CmisStorageException extends CmisBaseException
{
}
