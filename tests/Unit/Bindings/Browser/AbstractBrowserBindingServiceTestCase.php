<?php
namespace Dkd\PhpCmis\Test\Unit\Bindings\Browser;

/**
 * This file is part of php-cmis-client
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Bindings\BindingSessionInterface;
use Dkd\PhpCmis\SessionParameter;
use Dkd\PhpCmis\Test\Unit\FixtureHelperTrait;
use Dkd\PhpCmis\Test\Unit\ReflectionHelperTrait;
use PHPUnit_Framework_MockObject_MockObject;

abstract class AbstractBrowserBindingServiceTestCase extends \PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;
    use FixtureHelperTrait;

    const BROWSER_URL_TEST = 'http://foo.bar.baz';
    const TYPE_DEFINITION_CACHE_CLASS = 'http://foo.bar.baz';

    /**
     * Returns a mock of a BindingSessionInterface
     *
     * @param array $sessionParameterMap
     * @return BindingSessionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getSessionMock($sessionParameterMap = array())
    {
        $map = array(
            array(SessionParameter::BROWSER_SUCCINCT, null, false),
            array(SessionParameter::BROWSER_URL, null, self::BROWSER_URL_TEST),
            array(SessionParameter::TYPE_DEFINITION_CACHE_CLASS, null, '\\Doctrine\\Common\\Cache\\ArrayCache')
        );

        $map = array_merge($sessionParameterMap, $map);

        $sessionMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface'
        )->setMethods(array('get'))->getMockForAbstractClass();

        $sessionMock->expects($this->any())->method('get')->will($this->returnValueMap($map));

        return $sessionMock;
    }
}
