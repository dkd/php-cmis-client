<?php
namespace Dkd\PhpCmis\Test\Unit\Bindings;

use Dkd\PhpCmis\Bindings\CmisBindingsHelper;
use Dkd\PhpCmis\Enum\BindingType;
use Dkd\PhpCmis\SessionParameter;

/**
 * This file is part of php-cmis-client
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CmisBindingsHelperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CmisBindingsHelper
     */
    protected $cmisBindingsHelper;

    public function setUp()
    {
        $this->cmisBindingsHelper = new CmisBindingsHelper();
    }

    public function testCreateBindingThrowsExceptionIfNoSessionParametersAreGiven()
    {
        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            'Session parameters must be set!'
        );
        $this->cmisBindingsHelper->createBinding(array());
    }

    public function testCreateBindingThrowsExceptionIfNoBindingTypeSessionParameterIsGiven()
    {
        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            'Required binding type is not configured!'
        );
        $this->cmisBindingsHelper->createBinding(array('foo' => 'bar'));
    }

    public function testCreateBindingThrowsExceptionIfInvalidBindingTypeSessionParameterIsGiven()
    {
        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            'Invalid binding type given: bar'
        );
        $this->cmisBindingsHelper->createBinding(array(SessionParameter::BINDING_TYPE => 'bar'));
    }

    public function testCreateBindingThrowsExceptionIfGivenBindingTypeIsNotYetImplemented()
    {
        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            sprintf(
                'The given binding "%s" is not yet implemented.',
                BindingType::CUSTOM
            )
        );
        $this->cmisBindingsHelper->createBinding(array(SessionParameter::BINDING_TYPE => BindingType::CUSTOM));
    }

    public function testCreateBindingRequestsBindingFactoryForRequestedBinding()
    {
        $parameters = array(SessionParameter::BINDING_TYPE => BindingType::BROWSER);

        $cmisBindingsHelper = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getCmisBindingFactory')
        )->getMock();

        $cmisBindingFactoryMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingFactory')->setMethods(
            array('createCmisBrowserBinding')
        )->getMock();

        $cmisBindingFactoryMock->expects($this->once())->method('createCmisBrowserBinding')->with(
            $parameters
        )->willReturn(
            $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBinding')
                ->disableOriginalConstructor()
                ->getMockForAbstractClass()
        );

        $cmisBindingsHelper->expects($this->once())->method('getCmisBindingFactory')->willReturn(
            $cmisBindingFactoryMock
        );

        $cmisBindingsHelper->createBinding(
            $parameters
        );
    }

    public function testGetSpiReturnsInstanceOfBindingClassAndStoresItToTheSession()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get', 'put')
        )->getMockForAbstractClass();

        $bindingClassFixture = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Bindings\\CmisInterface');
        $bindingClassFixtureClassName = get_class($bindingClassFixture);

        // ensure that $session->get() is called 2 times. Once to check if spi exists already in the session
        // and second to get the name of the binding class that should be used for the spi.
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(CmisBindingsHelper::SPI_OBJECT, null, null),
                    array(SessionParameter::BINDING_CLASS, null, $bindingClassFixtureClassName)
                )
            )
        );

        // check if the binding is put into the session
        $sessionMock->expects($this->once())->method('put')->with(
            CmisBindingsHelper::SPI_OBJECT,
            $this->isInstanceOf($bindingClassFixtureClassName)
        );

        $spi = $this->cmisBindingsHelper->getSpi($sessionMock);

        $this->assertInstanceOf($bindingClassFixtureClassName, $spi);
    }

    public function testGetSpiReturnsSpiFromSessionIfAlreadyExists()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();

        $bindingClassFixture = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Bindings\\CmisInterface');

        $sessionMock->expects($this->once())->method('get')->with(CmisBindingsHelper::SPI_OBJECT)->willReturn(
            $bindingClassFixture
        );

        $this->assertSame($bindingClassFixture, $this->cmisBindingsHelper->getSpi($sessionMock));
    }

    public function testGetSpiThrowsExceptionIfBindingClassIsNotConfiguredInSession()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(CmisBindingsHelper::SPI_OBJECT, null, null),
                    array(SessionParameter::BINDING_CLASS, null, null)
                )
            )
        );

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            'The given binding class "" is not valid!'
        );
        $this->cmisBindingsHelper->getSpi($sessionMock);
    }

    public function testGetSpiThrowsExceptionIfGivenBindingClassDoesNotExist()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(CmisBindingsHelper::SPI_OBJECT, null, null),
                    array(SessionParameter::BINDING_CLASS, null, 'ThisClassDoesNotExist')
                )
            )
        );

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            'The given binding class "ThisClassDoesNotExist" is not valid!'
        );
        $this->cmisBindingsHelper->getSpi($sessionMock);
    }

    public function testGetSpiThrowsExceptionIfGivenBindingClassCouldNotBeInstantiated()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();

        $spiClassName = '\\Dkd\\PhpCmis\\Test\\Fixtures\\Php\\Bindings\\CmisBindingConstructorThrowsException';
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(CmisBindingsHelper::SPI_OBJECT, null, null),
                    array(SessionParameter::BINDING_CLASS, null, $spiClassName)
                )
            )
        );

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            sprintf('Could not create object of type "%s"!', $spiClassName)
        );
        $this->cmisBindingsHelper->getSpi($sessionMock);
    }

    public function testGetSpiThrowsExceptionIfGivenBindingClassDoesNotImplementCmisInterface()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();

        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(CmisBindingsHelper::SPI_OBJECT, null, null),
                    array(SessionParameter::BINDING_CLASS, null, 'stdClass')
                )
            )
        );

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            'The given binding class "stdClass" does not implement required CmisInterface!'
        );
        $this->cmisBindingsHelper->getSpi($sessionMock);
    }

    public function testGetHttpInvokerReturnsInstanceOfInvokerClassAndStoresItToTheSession()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get', 'put')
        )->getMockForAbstractClass();

        // ensure that $session->get() is called 2 times. Once to check if http invoker exists already in the session
        // and second to get the name of the http invoker class that should be used.
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(SessionParameter::HTTP_INVOKER_OBJECT, null, null),
                    array(SessionParameter::HTTP_INVOKER_CLASS, null, '\\GuzzleHttp\\Client')
                )
            )
        );

        // check if the binding is put into the session
        $sessionMock->expects($this->once())->method('put')->with(
            SessionParameter::HTTP_INVOKER_OBJECT,
            $this->isInstanceOf('\\GuzzleHttp\\Client')
        );

        $httpInvoker = $this->cmisBindingsHelper->getHttpInvoker($sessionMock);

        $this->assertInstanceOf('\\GuzzleHttp\\Client', $httpInvoker);
    }

    public function testGetHttpInvokerReturnsHttpInvokerFromSessionIfAlreadyExists()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();

        $httpInvokerFixture = $this->getMock('\\GuzzleHttp\\ClientInterface');

        $sessionMock->expects($this->once())->method('get')->with(SessionParameter::HTTP_INVOKER_OBJECT)->willReturn(
            $httpInvokerFixture
        );

        $this->assertSame($httpInvokerFixture, $this->cmisBindingsHelper->getHttpInvoker($sessionMock));
    }

    public function testGetHttpInvokerThrowsExceptionIfInvokerDoesNotImplementExpectedInterface()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();

        $httpInvokerFixture = $this->getMock('\\stdClass');

        $sessionMock->expects($this->once())->method('get')->with(SessionParameter::HTTP_INVOKER_OBJECT)->willReturn(
            $httpInvokerFixture
        );

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
            '',
            1415281262
        );
        $this->assertSame($httpInvokerFixture, $this->cmisBindingsHelper->getHttpInvoker($sessionMock));
    }

    public function testGetHttpInvokerThrowsExceptionIfHttpInvokerClassIsNotConfiguredInSession()
    {
        /** @var \Dkd\PhpCmis\Bindings\BindingSessionInterface|\PHPUnit_Framework_MockObject_MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(SessionParameter::HTTP_INVOKER_OBJECT, null, null),
                    array(SessionParameter::HTTP_INVOKER_CLASS, null, null)
                )
            )
        );

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            'The given HTTP Invoker class "" is not valid!'
        );
        $this->cmisBindingsHelper->getHttpInvoker($sessionMock);
    }

    public function testGetHttpInvokerThrowsExceptionIfGivenHttpInvokerClassDoesNotExist()
    {
        /** @var \Dkd\PhpCmis\Bindings\BindingSessionInterface|\PHPUnit_Framework_MockObject_MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(SessionParameter::HTTP_INVOKER_OBJECT, null, null),
                    array(SessionParameter::HTTP_INVOKER_CLASS, null, 'ThisClassDoesNotExist')
                )
            )
        );

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            'The given HTTP Invoker class "ThisClassDoesNotExist" is not valid!'
        );
        $this->cmisBindingsHelper->getHttpInvoker($sessionMock);
    }

    public function testGetHttpInvokerThrowsExceptionIfGivenHttpInvokerClassCouldNotBeInstantiated()
    {
        /** @var \Dkd\PhpCmis\Bindings\BindingSessionInterface|\PHPUnit_Framework_MockObject_MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();

        $httpInvokerClassName = '\\Dkd\\PhpCmis\\Test\\Fixtures\\Php\\HttpInvokerConstructorThrowsException';
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(SessionParameter::HTTP_INVOKER_OBJECT, null, null),
                    array(SessionParameter::HTTP_INVOKER_CLASS, null, $httpInvokerClassName)
                )
            )
        );

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            sprintf('Could not create object of type "%s"!', $httpInvokerClassName)
        );
        $this->cmisBindingsHelper->getHttpInvoker($sessionMock);
    }

    public function testGetJsonConverterReturnsInstanceOfConverterClassAndStoresItToTheSession()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get', 'put')
        )->getMockForAbstractClass();

        // ensure that $session->get() is called 2 times. Once to check if JSON Converter exists already in the session
        // and second to get the name of the JSON Converter class that should be used.
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(SessionParameter::JSON_CONVERTER, null, null),
                    array(SessionParameter::JSON_CONVERTER_CLASS, null, '\\GuzzleHttp\\Client')
                )
            )
        );

        // check if the binding is put into the session
        $sessionMock->expects($this->once())->method('put')->with(
            SessionParameter::JSON_CONVERTER,
            $this->isInstanceOf('\\GuzzleHttp\\Client')
        );

        $jsonConverter = $this->cmisBindingsHelper->getJsonConverter($sessionMock);

        $this->assertInstanceOf('\\GuzzleHttp\\Client', $jsonConverter);
    }

    public function testGetJsonConverterReturnsJsonConverterFromSessionIfAlreadyExists()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();

        $jsonConverterFixture = $this->getMock('\\stdClass');

        $sessionMock->expects($this->once())->method('get')->with(SessionParameter::JSON_CONVERTER)->willReturn(
            $jsonConverterFixture
        );

        $this->assertSame($jsonConverterFixture, $this->cmisBindingsHelper->getJsonConverter($sessionMock));
    }

    public function testGetJsonConverterThrowsExceptionIfJsonConverterClassIsNotConfiguredInSession()
    {
        /** @var \Dkd\PhpCmis\Bindings\BindingSessionInterface|\PHPUnit_Framework_MockObject_MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(SessionParameter::JSON_CONVERTER, null, null),
                    array(SessionParameter::JSON_CONVERTER_CLASS, null, null)
                )
            )
        );

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            'The given JSON Converter class "" is not valid!'
        );
        $this->cmisBindingsHelper->getJsonConverter($sessionMock);
    }

    public function testGetJsonConverterThrowsExceptionIfGivenJsonConverterClassDoesNotExist()
    {
        /** @var \Dkd\PhpCmis\Bindings\BindingSessionInterface|\PHPUnit_Framework_MockObject_MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(SessionParameter::JSON_CONVERTER, null, null),
                    array(SessionParameter::JSON_CONVERTER_CLASS, null, 'ThisClassDoesNotExist')
                )
            )
        );

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            'The given JSON Converter class "ThisClassDoesNotExist" is not valid!'
        );
        $this->cmisBindingsHelper->getJsonConverter($sessionMock);
    }

    public function testGetJsonConverterThrowsExceptionIfGivenJsonConverterClassCouldNotBeInstantiated()
    {
        /** @var \Dkd\PhpCmis\Bindings\BindingSessionInterface|\PHPUnit_Framework_MockObject_MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface')->setMethods(
            array('get')
        )->getMockForAbstractClass();

        $jsonConverterClassName = '\\Dkd\\PhpCmis\\Test\\Fixtures\\Php\\ConstructorThrowsException';
        $sessionMock->expects($this->exactly(2))->method('get')->will(
            $this->returnValueMap(
                array(
                    array(SessionParameter::JSON_CONVERTER, null, null),
                    array(SessionParameter::JSON_CONVERTER_CLASS, null, $jsonConverterClassName)
                )
            )
        );

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            sprintf('Could not create object of type "%s"!', $jsonConverterClassName)
        );
        $this->cmisBindingsHelper->getJsonConverter($sessionMock);
    }
}
