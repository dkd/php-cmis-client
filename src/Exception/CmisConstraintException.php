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
 * CMIS Constraint Exception.
 *
 * Intent: The operation violates a repository- or object-level constraint
 * defined in the CMIS domain model.
 */
class CmisConstraintException extends CmisBaseException
{
}
