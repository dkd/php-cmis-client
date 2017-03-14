<?php
namespace Dkd\PhpCmis\Exception;

/*
 * This file is part of php-cmis-client.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Unauthorized exception.
 *
 * (This is exception is not defined in the CMIS specification and is therefore derived from
 * {@link CmisRuntimeException}.)
 */
class CmisUnauthorizedException extends CmisRuntimeException
{
}
