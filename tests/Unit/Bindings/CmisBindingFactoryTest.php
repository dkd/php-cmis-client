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
use Dkd\PhpCmis\Test\Unit\ReflectionHelperTrait;

class CmisBindingFactoryTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;

    /**
     * @var CmisBindingFactory
     */
    protected $cmisBindingFactory;

    /**
     * @var string
     */
    const CLASS_TO_TEST = '\\Dkd\\PhpCmis\\Bindings\\CmisBindingFactory';

    public function setUp()
    {
        $className = self::CLASS_TO_TEST;
        $this->cmisBindingFactory = new $className();
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
        $validateMethod = $this->getMethod(self::CLASS_TO_TEST, 'validateCmisBrowserBindingParameters');
        $validateMethod->invokeArgs($this->cmisBindingFactory, array(&$sessionParameters));
    }

    public function testValidateCmisBrowserBindingParametersAddsDefaultValueForBindingClass()
    {
        $sessionParameters = array(SessionParameter::BROWSER_URL => 'foo');

        $validateMethod = $this->getMethod(self::CLASS_TO_TEST, 'validateCmisBrowserBindingParameters');
        $validateMethod->invokeArgs($this->cmisBindingFactory, array(&$sessionParameters));

        $this->assertArrayHasKey(SessionParameter::BINDING_CLASS, $sessionParameters);
    }

    public function testValidateCmisBrowserBindingParametersSetsBrowserSuccinctTrueIfNotSet()
    {
        $sessionParameters = array(SessionParameter::BROWSER_URL => 'foo');

        $validateMethod = $this->getMethod(self::CLASS_TO_TEST, 'validateCmisBrowserBindingParameters');
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
}
