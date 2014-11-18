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
 * CMIS FilterNotValid Exception.
 *
 * Intent: The property filter or rendition filter input to the operation is not
 * valid. The repository SHOULD NOT throw this exception if the filter syntax is
 * correct but one or more elements in the filter is unknown. Unknown elements
 * SHOULD be ignored.
 */
class CmisFilterNotValidException extends CmisBaseException
{
}
