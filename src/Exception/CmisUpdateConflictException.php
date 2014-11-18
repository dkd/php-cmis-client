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
 * CMIS UpdateConflict Exception.
 *
 * Intent: The operation is attempting to update an object that is no longer
 * current (as determined by the repository).
 */
class CmisUpdateConflictException extends CmisBaseException
{
}
