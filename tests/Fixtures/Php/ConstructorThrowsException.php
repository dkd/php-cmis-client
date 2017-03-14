<?php
namespace Dkd\PhpCmis\Test\Fixtures\Php;

/*
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class simply throws an exception when initiated
 */
class ConstructorThrowsException
{
    public function __construct()
    {
        throw new \Exception('This class can not be instantiated!');
    }
}
