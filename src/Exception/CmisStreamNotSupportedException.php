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
 * CMIS StreamNotSupported Exception.
 *
 * Intent: The operation is attempting to get or set a content stream for a
 * document whose object-type specifies that a content stream is not allowed for
 * documents of that type.
 */
class CmisStreamNotSupportedException extends CmisBaseException
{
}
