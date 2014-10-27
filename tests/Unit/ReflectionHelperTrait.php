<?php
namespace Dkd\PhpCmis\Test\Unit;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

trait ReflectionHelperTrait
{
    /**
     * Get a reflection of a method and make it accessible. This is used to call protected methods
     * in unit tests.
     *
     * @param string $class The class to get the method from
     * @param string $method The name of the method to get.
     * @return \ReflectionMethod
     */
    protected function getMethod($class, $method)
    {
        $class = new \ReflectionClass($class);
        $method = $class->getMethod($method);
        $method->setAccessible(true);

        return $method;
    }
}
