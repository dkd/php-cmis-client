<?php
namespace Dkd\PhpCmis\Test\Unit\Bindings;

/**
 * This file is part of php-cmis-client
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Bindings\CmisBindingFactory;
use Dkd\PhpCmis\SessionParameter;

class CmisBindingFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CmisBindingFactory
     */
    protected $cmisBindingFactory;

    public function setUp()
    {
        $this->cmisBindingFactory = new CmisBindingFactory();
    }

    public function testCreateCmisBrowserBindingThrowsExceptionIfBrowserUrlIsNotConfigured()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $this->cmisBindingFactory->createCmisBrowserBinding(array('foo' => 'bar'));
    }

    public function testValidateCmisBrowserBindingParametersThrowsExceptionIfBrowserUrlIsNotConfigured()
    {
        $sessionParameters = array();

        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $validateMethod = self::getMethod('validateCmisBrowserBindingParameters');
        $validateMethod->invokeArgs($this->cmisBindingFactory, array(&$sessionParameters));
    }

    public function testValidateCmisBrowserBindingParametersAddsDefaultValueForBindingClass()
    {
        $sessionParameters = array(SessionParameter::BROWSER_URL => 'foo');

        $validateMethod = self::getMethod('validateCmisBrowserBindingParameters');
        $validateMethod->invokeArgs($this->cmisBindingFactory, array(&$sessionParameters));

        $this->assertArrayHasKey(SessionParameter::BINDING_CLASS, $sessionParameters);
    }

    public function testValidateCmisBrowserBindingParametersSetsBrowserSuccinctTrueIfNotSet()
    {
        $sessionParameters = array(SessionParameter::BROWSER_URL => 'foo');

        $validateMethod = self::getMethod('validateCmisBrowserBindingParameters');
        $validateMethod->invokeArgs($this->cmisBindingFactory, array(&$sessionParameters));

        $this->assertArrayHasKey(SessionParameter::BROWSER_SUCCINCT, $sessionParameters);
        $this->assertTrue($sessionParameters[SessionParameter::BROWSER_SUCCINCT]);

        // ensure that BROWSER_SUCCINCT is not modified after it has been assigned to the session
        $sessionParameters[SessionParameter::BROWSER_SUCCINCT] = 'FOOBAR';
        $validateMethod->invokeArgs($this->cmisBindingFactory, array(&$sessionParameters));
        $this->assertEquals('FOOBAR', $sessionParameters[SessionParameter::BROWSER_SUCCINCT]);
    }

    public function testCreateCmisBrowserBindingReturnsCmisBinding()
    {
        $sessionParameters = array(SessionParameter::BROWSER_URL => 'foo');
        $browserBinding = $this->cmisBindingFactory->createCmisBrowserBinding($sessionParameters);
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\Bindings\\CmisBinding', $browserBinding);
    }

    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('\\Dkd\\PhpCmis\\Bindings\\CmisBindingFactory');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }
}
