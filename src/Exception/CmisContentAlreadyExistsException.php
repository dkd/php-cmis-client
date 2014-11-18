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
 * CMIS ContentAlreadyExists Exception.
 *
 * Intent: The operation attempts to set the content stream for a document that
 * already has a content stream without explicitly specifying the
 * "overwriteFlag" parameter.
 */
class CmisContentAlreadyExistsException extends CmisBaseException
{
}
