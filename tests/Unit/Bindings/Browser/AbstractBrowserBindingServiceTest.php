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

use Dkd\PhpCmis\Bindings\Browser\AbstractBrowserBindingService;
use Dkd\PhpCmis\Bindings\Browser\RepositoryUrlCache;
use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\DataObjects\AccessControlEntry;
use Dkd\PhpCmis\DataObjects\AccessControlList;
use Dkd\PhpCmis\DataObjects\Principal;
use Dkd\PhpCmis\DataObjects\Properties;
use Dkd\PhpCmis\DataObjects\PropertyBoolean;
use Dkd\PhpCmis\DataObjects\PropertyDateTime;
use Dkd\PhpCmis\DataObjects\PropertyDecimal;
use Dkd\PhpCmis\DataObjects\PropertyId;
use Dkd\PhpCmis\DataObjects\PropertyString;
use Dkd\PhpCmis\DataObjects\RepositoryInfoBrowserBinding;
use Dkd\PhpCmis\Enum\DateTimeFormat;
use Dkd\PhpCmis\SessionParameter;
use Guzzle\Http\EntityBody;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Response;
use League\Url\Url;
use PHPUnit_Framework_MockObject_MockObject;

class AbstractBrowserBindingServiceTest extends AbstractBrowserBindingServiceTestCase
{
    const CLASS_TO_TEST = '\\Dkd\\PhpCmis\\Bindings\\Browser\\AbstractBrowserBindingService';

    public function testConstructorSetsSessionAndBindingsHelper()
    {
        $sessionMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface'
        )->setMockClassName('SessionMock')->getMockForAbstractClass();

        $bindingHelper = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMockClassName(
            'BindingHelperMock'
        )->getMockForAbstractClass();

        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock, $bindingHelper))->getMockForAbstractClass();

        $this->assertAttributeInstanceOf('SessionMock', 'session', $binding);
        $this->assertAttributeInstanceOf('BindingHelperMock', 'cmisBindingsHelper', $binding);
    }

    public function testConstructorSetsSuccinctPropertyBasedOnSessionParameter()
    {
        $sessionMock = $this->getSessionMock();

        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertAttributeSame(false, 'succinct', $binding);

        $sessionMock = $this->getSessionMock(array(array(SessionParameter::BROWSER_SUCCINCT, null, true)));

        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertAttributeSame(true, 'succinct', $binding);
    }

    public function testConstructorCreatesCmisBindingHelperInstanceIfNoneGiven()
    {
        $sessionMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface'
        )->setMethods(array('get'))->getMockForAbstractClass();

        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertAttributeInstanceOf(
            '\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper',
            'cmisBindingsHelper',
            $binding
        );
    }

    public function testGetSuccinctReturnsPropertyValue()
    {
        $sessionMock = $this->getSessionMock();

        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertAttributeSame(
            $this->getMethod(self::CLASS_TO_TEST, 'getSuccinct')->invoke($binding),
            'succinct',
            $binding
        );
    }

    public function testGetSessionReturnsSession()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertSame($sessionMock, $binding->getSession());
    }

    public function testGetHttpInvokerGetsHttpInvokerFromCmisBindingsHelper()
    {
        $httpInvokerDummy = new Client();
        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getHttpInvoker')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getHttpInvoker')->willReturn($httpInvokerDummy);

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock(), $cmisBindingsHelperMock))->getMockForAbstractClass();

        $this->assertSame($httpInvokerDummy, $this->getMethod(self::CLASS_TO_TEST, 'getHttpInvoker')->invoke($binding));
    }

    public function testGetRepositoryUrlThrowsExceptionIfRepositoryDoesNotExist()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->setMethods(
            array('getRepositoriesInternal', 'getRepositoryUrlCache')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryUrlCache'
        )->setMethods(array('getRepositoryUrl'))->getMock();

        $repositoryUrlCacheMock->expects($this->exactly(2))->method('getRepositoryUrl')->willReturn(null);
        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->once())->method('getRepositoriesInternal');

        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisObjectNotFoundException');

        $this->getMethod(self::CLASS_TO_TEST, 'getRepositoryUrl')->invokeArgs($binding, array('repository-id'));
    }

    public function testGetRepositoryUrlReturnsUrlFromRepositoryUrlCachet()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->setMethods(
            array('getRepositoriesInternal', 'getRepositoryUrlCache')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryUrlCache'
        )->setMethods(array('getRepositoryUrl'))->getMock();

        $url = Url::createFromUrl(self::BROWSER_URL_TEST);

        $repositoryUrlCacheMock->expects($this->once())->method('getRepositoryUrl')->willReturn($url);
        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);

        $this->assertSame(
            $url,
            $this->getMethod(self::CLASS_TO_TEST, 'getRepositoryUrl')->invokeArgs($binding, array('repository-id'))
        );
    }

    public function testGetObjectUrlThrowsExceptionIfRepositoryDoesNotExist()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->setMethods(
            array('getRepositoriesInternal', 'getRepositoryUrlCache')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryUrlCache'
        )->setMethods(array('getObjectUrl'))->getMock();

        $repositoryUrlCacheMock->expects($this->exactly(2))->method('getObjectUrl')->willReturn(null);
        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->once())->method('getRepositoriesInternal');

        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisObjectNotFoundException');

        $this->getMethod(self::CLASS_TO_TEST, 'getObjectUrl')->invokeArgs(
            $binding,
            array('repository-id', 'object-id')
        );
    }

    public function testGetObjectUrlReturnsUrlFromRepositoryUrlCache()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->setMethods(
            array('getRepositoriesInternal', 'getRepositoryUrlCache')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryUrlCache'
        )->setMethods(array('getObjectUrl'))->getMock();

        $url = Url::createFromUrl(self::BROWSER_URL_TEST);

        $repositoryUrlCacheMock->expects($this->once())->method('getObjectUrl')->willReturn($url);
        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);

        $this->assertSame(
            $url,
            $this->getMethod(self::CLASS_TO_TEST, 'getObjectUrl')->invokeArgs(
                $binding,
                array('repository-id', 'object-id')
            )
        );
    }

    public function testGetPathUrlThrowsExceptionIfRepositoryDoesNotExist()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->setMethods(
            array('getRepositoriesInternal', 'getRepositoryUrlCache')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryUrlCache'
        )->setMethods(array('getPathUrl'))->getMock();

        $repositoryUrlCacheMock->expects($this->exactly(2))->method('getPathUrl')->willReturn(null);
        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->once())->method('getRepositoriesInternal');

        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisObjectNotFoundException');

        $this->getMethod(self::CLASS_TO_TEST, 'getPathUrl')->invokeArgs($binding, array('repository-id', 'path'));
    }

    public function testGetPathUrlReturnsUrlFromRepositoryUrlCachet()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->setMethods(
            array('getRepositoriesInternal', 'getRepositoryUrlCache')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryUrlCache'
        )->setMethods(array('getPathUrl'))->getMock();

        $url = Url::createFromUrl(self::BROWSER_URL_TEST);

        $repositoryUrlCacheMock->expects($this->once())->method('getPathUrl')->willReturn($url);
        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);

        $this->assertSame(
            $url,
            $this->getMethod(self::CLASS_TO_TEST, 'getPathUrl')->invokeArgs($binding, array('repository-id', 'path'))
        );
    }

    public function testGetServiceUrlReturnsServiceUrlStringFromSession()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock()))->getMockForAbstractClass();

        $this->assertSame(
            self::BROWSER_URL_TEST,
            $this->getMethod(self::CLASS_TO_TEST, 'getServiceUrl')->invoke($binding)
        );
    }

    public function testGetServiceUrlReturnsNullIfNoStringCouldBeFetchedFromSession()
    {
        $map = array(
            array(SessionParameter::BROWSER_SUCCINCT, null, false),
            array(SessionParameter::BROWSER_URL, null, 0)
        );

        $sessionMock = $this->getSessionMock($map);

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertNull($this->getMethod(self::CLASS_TO_TEST, 'getServiceUrl')->invoke($binding));
    }

    /**
     * @dataProvider statusCodeExceptionDataProvider
     * @param $expectedException
     * @param $errorCode
     * @param $message
     */
    public function testConvertStatusCodeReturnsExceptionBasedOnResponseMessageOrErrorCode(
        $expectedException,
        $errorCode,
        $message
    ) {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertInstanceOf(
            $expectedException,
            $this->getMethod(self::CLASS_TO_TEST, 'convertStatusCode')->invokeArgs(
                $binding,
                array($errorCode, $message)
            )
        );
    }

    public function statusCodeExceptionDataProvider()
    {
        return array(
            // based on message
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisConstraintException',
                0,
                '{"message":"Error message","exception":"constraint"}'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisContentAlreadyExistsException',
                0,
                '{"message":"Error message","exception":"contentAlreadyExists"}'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisFilterNotValidException',
                0,
                '{"message":"Error message","exception":"filterNotValid"}'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                0,
                '{"message":"Error message","exception":"invalidArgument"}'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisNameConstraintViolationException',
                0,
                '{"message":"Error message","exception":"nameConstraintViolation"}'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisNotSupportedException',
                0,
                '{"message":"Error message","exception":"notSupported"}'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisObjectNotFoundException',
                0,
                '{"message":"Error message","exception":"objectNotFound"}'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisPermissionDeniedException',
                0,
                '{"message":"Error message","exception":"permissionDenied"}'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisStorageException',
                0,
                '{"message":"Error message","exception":"storage"}'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisStreamNotSupportedException',
                0,
                '{"message":"Error message","exception":"streamNotSupported"}'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisUpdateConflictException',
                0,
                '{"message":"Error message","exception":"updateConflict"}'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisVersioningException',
                0,
                '{"message":"Error message","exception":"versioning"}'
            ),
            // unknown exception name
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
                399,
                '{"message":"Error message","exception":"unknownExceptionNameFooBar"}'
            ),
            // bases on status code
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisConnectionException',
                301,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisConnectionException',
                302,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisConnectionException',
                303,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisConnectionException',
                307,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                400,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisUnauthorizedException',
                401,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisPermissionDeniedException',
                403,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisObjectNotFoundException',
                404,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisNotSupportedException',
                405,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisProxyAuthenticationException',
                407,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisConstraintException',
                409,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
                500,
                'error message'
            ),
            array(
                '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
                9999,
                'error message'
            ),
        );
    }

    public function testReadCallsHttpInvokerAndReturnsRequestResult()
    {
        $sessionMock = $this->getSessionMock();

        $testUrl = Url::createFromUrl(self::BROWSER_URL_TEST);
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor()->getMock(
        );
        $httpInvokerMock = $this->getMockBuilder('\\GuzzleHttp\\Client')->disableOriginalConstructor()->setMethods(
            array('get')
        )->getMock();
        $httpInvokerMock->expects($this->once())->method('get')->with((string) $testUrl)->willReturn(
            $responseMock
        );

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->setMethods(array('getHttpInvoker'))->getMockForAbstractClass();
        $binding->expects($this->any())->method('getHttpInvoker')->willReturn($httpInvokerMock);

        $this->assertSame(
            $responseMock,
            $this->getMethod(self::CLASS_TO_TEST, 'read')->invokeArgs($binding, array($testUrl))
        );
    }

    public function testReadCatchesAllRequestExceptionsAndConvertsThemToACmisException()
    {
        $sessionMock = $this->getSessionMock();

        $testUrl = Url::createFromUrl(self::BROWSER_URL_TEST);
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor()->getMock(
        );
        $httpInvokerMock = $this->getMockBuilder('\\GuzzleHttp\\Client')->disableOriginalConstructor()->setMethods(
            array('get')
        )->getMock();
        /** @var RequestException|PHPUnit_Framework_MockObject_MockObject $exceptionMock */
        $exceptionMock = $this->getMockBuilder('\\GuzzleHttp\\Exception\\RequestException')->disableOriginalConstructor(
        )->setMethods(array('getResponse'))->getMock();
        $exceptionMock->expects($this->any())->method('getResponse')->willReturn($responseMock);
        $httpInvokerMock->expects($this->once())->method('get')->with($testUrl)->willThrowException(
            $exceptionMock
        );

        $this->setExpectedException(get_class($exceptionMock));

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->setMethods(
            array('getHttpInvoker', 'convertStatusCode')
        )->getMockForAbstractClass();
        $binding->expects($this->any())->method('getHttpInvoker')->willReturn($httpInvokerMock);
        $binding->expects($this->any())->method('convertStatusCode')->willReturn($exceptionMock);

        $this->assertSame(
            $responseMock,
            $this->getMethod(self::CLASS_TO_TEST, 'read')->invokeArgs($binding, array($testUrl))
        );
    }

    public function testReadCatchesRequestExceptionAndPassesOnlyExceptionIfNoRequestDataExists()
    {
        $sessionMock = $this->getSessionMock();

        $testUrl = Url::createFromUrl(self::BROWSER_URL_TEST);
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor()->getMock(
        );
        $httpInvokerMock = $this->getMockBuilder('\\GuzzleHttp\\Client')->disableOriginalConstructor()->setMethods(
            array('get')
        )->getMock();
        /** @var RequestException|PHPUnit_Framework_MockObject_MockObject $exceptionMock */
        $exceptionMock = new \GuzzleHttp\Exception\RequestException(
            'dummy message',
            $this->getMockForAbstractClass('\\GuzzleHttp\\Message\\RequestInterface')
        );
        $httpInvokerMock->expects($this->once())->method('get')->with($testUrl)->willThrowException(
            $exceptionMock
        );

        $this->setExpectedException(get_class($exceptionMock));

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->setMethods(
            array('getHttpInvoker', 'convertStatusCode')
        )->getMockForAbstractClass();
        $binding->expects($this->any())->method('getHttpInvoker')->willReturn($httpInvokerMock);
        $binding->expects($this->any())->method('convertStatusCode')->with(0, null, $exceptionMock)->willReturn(
            $exceptionMock
        );

        $this->assertSame(
            $responseMock,
            $this->getMethod(self::CLASS_TO_TEST, 'read')->invokeArgs($binding, array($testUrl))
        );
    }

    public function testPostCallsHttpInvokerAndReturnsRequestResult()
    {
        $testUrl = Url::createFromUrl(self::BROWSER_URL_TEST);
        $content = 'fooBarBaz';

        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor()->getMock(
        );
        $httpInvokerMock = $this->getMockBuilder('\\GuzzleHttp\\Client')->disableOriginalConstructor()->setMethods(
            array('post')
        )->getMock();
        $httpInvokerMock->expects($this->once())->method('post')->with(
            $testUrl,
            array('body' => $content)
        )->willReturn($responseMock);

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock()))->setMethods(
            array('getHttpInvoker')
        )->getMockForAbstractClass();
        $binding->expects($this->any())->method('getHttpInvoker')->willReturn($httpInvokerMock);

        $this->assertSame(
            $responseMock,
            $this->getMethod(self::CLASS_TO_TEST, 'post')->invokeArgs($binding, array($testUrl, $content))
        );
    }

    public function testPostCatchesAllRequestExceptionsAndConvertsThemToACmisException()
    {
        $testUrl = Url::createFromUrl(self::BROWSER_URL_TEST);
        $content = 'fooBarBaz';

        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor()->getMock(
        );
        $httpInvokerMock = $this->getMockBuilder('\\GuzzleHttp\\Client')->disableOriginalConstructor()->setMethods(
            array('post')
        )->getMock();
        /** @var RequestException|PHPUnit_Framework_MockObject_MockObject $exceptionMock */
        $exceptionMock = $this->getMockBuilder('\\GuzzleHttp\\Exception\\RequestException')->disableOriginalConstructor(
        )->setMethods(array('getResponse'))->getMock();
        $exceptionMock->expects($this->any())->method('getResponse')->willReturn($responseMock);
        $httpInvokerMock->expects($this->once())->method('post')->with(
            (string) $testUrl,
            array('body' => $content)
        )->willThrowException(
            $exceptionMock
        );

        $this->setExpectedException(get_class($exceptionMock));

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock()))->setMethods(
            array('getHttpInvoker', 'convertStatusCode')
        )->getMockForAbstractClass();
        $binding->expects($this->any())->method('getHttpInvoker')->willReturn($httpInvokerMock);
        $binding->expects($this->any())->method('convertStatusCode')->willReturn($exceptionMock);

        $this->assertSame(
            $responseMock,
            $this->getMethod(self::CLASS_TO_TEST, 'post')->invokeArgs($binding, array($testUrl, $content))
        );
    }

    public function testGetRepositoryUrlCacheGetsRepositoryUrlCacheFromSession()
    {
        $repositoryUrlCache = new RepositoryUrlCache();
        $sessionMock = $this->getSessionMock(
            array(array(SessionParameter::REPOSITORY_URL_CACHE, null, $repositoryUrlCache))
        );

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertSame(
            $repositoryUrlCache,
            $this->getMethod(self::CLASS_TO_TEST, 'getRepositoryUrlCache')->invoke($binding)
        );
    }

    public function testGetRepositoryUrlCacheCreatesNewInstanceIfNoInstanceIsDefinedInSession()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock()))->getMockForAbstractClass();

        $this->assertInstanceOf(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryUrlCache',
            $this->getMethod(self::CLASS_TO_TEST, 'getRepositoryUrlCache')->invoke($binding)
        );
    }

    public function testGetTypeDefinitionInternalBuildsAndReadsUrlAndConvertsJsonResultToTypeDefinitionObject()
    {
        $repositoryId = 'repositoryId';
        $typeId = 'typeId';
        $dummyResponse = $this->getMock(Response::class, array('json'), array('{"foo": "bar"}'));

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertTypeDefinition')
        )->getMock();
        $jsonConverterMock->expects($this->once())->method('convertTypeDefinition')->willReturn('TypeDefinitionResult');
        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock(), $cmisBindingsHelperMock))->setMethods(
            array('getRepositoryUrl', 'read')
        )->getMockForAbstractClass();

        $urlDummy = Url::createFromUrl(self::BROWSER_URL_TEST . '?foo=bar');
        $expectedUrl = Url::createFromUrl(self::BROWSER_URL_TEST . '?foo=bar&typeId=typeId');

        $binding->expects($this->any())->method('getRepositoryUrl')->willReturn($urlDummy);
        $binding->expects($this->any())->method('read')->with($expectedUrl)->willReturn($dummyResponse);

        $this->assertSame(
            'TypeDefinitionResult',
            $this->getMethod(self::CLASS_TO_TEST, 'getTypeDefinitionInternal')->invokeArgs(
                $binding,
                array($repositoryId, $typeId)
            )
        );
    }

    public function testGetTypeDefinitionInternalThrowsExceptionIfRepositoryIdIsEmpty()
    {
        $repositoryId = null;
        $typeId = 'typeId';

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock(), $cmisBindingsHelperMock))->getMockForAbstractClass();

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
            'Repository id must not be empty!'
        );

        $this->getMethod(self::CLASS_TO_TEST, 'getTypeDefinitionInternal')->invokeArgs(
            $binding,
            array($repositoryId, $typeId)
        );
    }

    public function testGetTypeDefinitionInternalThrowsExceptionIfTypeIdIsEmpty()
    {
        $repositoryId = 'repositoryId';
        $typeId = null;

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock(), $cmisBindingsHelperMock))->getMockForAbstractClass();

        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
            'Type id must not be empty!'
        );
        $this->getMethod(self::CLASS_TO_TEST, 'getTypeDefinitionInternal')->invokeArgs(
            $binding,
            array($repositoryId, $typeId)
        );
    }

    public function testGetRepositoriesInternalThrowsExceptionIfRequestDoesNotReturnValidJson()
    {
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn(false);

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock()))->setMethods(
            array('getRepositoryUrlCache', 'getServiceUrl', 'read')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryUrlCache'
        )->setMethods(array('buildUrl'))->getMock();
        $repositoryUrlCacheMock->expects($this->any())->method('buildUrl')->willReturn(
            Url::createFromUrl(self::BROWSER_URL_TEST)
        );

        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->any())->method('getServiceUrl')->willReturn(self::BROWSER_URL_TEST);
        $binding->expects($this->any())->method('read')->willReturn($responseMock);

        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisConnectionException', '', 1416343166);
        $this->getMethod(self::CLASS_TO_TEST, 'getRepositoriesInternal')->invoke($binding);
    }

    public function testGetRepositoriesInternalThrowsExceptionIfRequestDoesNotReturnAnItemArray()
    {
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn(array('invalidValue'));

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock()))->setMethods(
            array('getRepositoryUrlCache', 'getServiceUrl', 'read')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryUrlCache'
        )->setMethods(array('buildUrl'))->getMock();
        $repositoryUrlCacheMock->expects($this->any())->method('buildUrl')->willReturn(
            Url::createFromUrl(self::BROWSER_URL_TEST)
        );

        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->any())->method('getServiceUrl')->willReturn(self::BROWSER_URL_TEST);
        $binding->expects($this->any())->method('read')->willReturn($responseMock);

        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisConnectionException', '', 1415187764);
        $this->getMethod(self::CLASS_TO_TEST, 'getRepositoriesInternal')->invoke($binding);
    }

    /**
     * @dataProvider incompleteRepositoryInfosDataProvider
     * @param $repositoryInfo
     */
    public function testGetRepositoriesInternalThrowsExceptionIfRepositoryInfosAreEmpty($repositoryInfo)
    {
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertRepositoryInfo')
        )->getMock();

        $jsonConverterMock->expects($this->once())->method('convertRepositoryInfo')->willReturn(
            $repositoryInfo
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn(array(array('valid repository info stuff')));

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock(), $cmisBindingsHelperMock))->setMethods(
            array('getRepositoryUrlCache', 'getServiceUrl', 'read')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryUrlCache'
        )->setMethods(array('buildUrl'))->getMock();
        $repositoryUrlCacheMock->expects($this->any())->method('buildUrl')->willReturn(
            Url::createFromUrl(self::BROWSER_URL_TEST)
        );

        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->any())->method('getServiceUrl')->willReturn(self::BROWSER_URL_TEST);
        $binding->expects($this->any())->method('read')->willReturn($responseMock);

        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisConnectionException', '', 1415187765);
        $this->getMethod(self::CLASS_TO_TEST, 'getRepositoriesInternal')->invoke($binding);
    }

    public function incompleteRepositoryInfosDataProvider()
    {
        $repositoryInfoMissingId = new RepositoryInfoBrowserBinding();
        $repositoryInfoMissingId->setRepositoryUrl(self::BROWSER_URL_TEST);
        $repositoryInfoMissingId->setRootUrl(self::BROWSER_URL_TEST);

        $repositoryInfoMissingRepositoryUrl = new RepositoryInfoBrowserBinding();
        $repositoryInfoMissingRepositoryUrl->setId('id');
        $repositoryInfoMissingRepositoryUrl->setRootUrl(self::BROWSER_URL_TEST);

        $repositoryInfoMissingRootUrl = new RepositoryInfoBrowserBinding();
        $repositoryInfoMissingRootUrl->setId('id');
        $repositoryInfoMissingRootUrl->setRepositoryUrl(self::BROWSER_URL_TEST);

        return array(
            array('repository Info Missing Id' => $repositoryInfoMissingId),
            array('repository Info Missing Repository Url' => $repositoryInfoMissingRepositoryUrl),
            array('repository Info Missing Root Url' => $repositoryInfoMissingRootUrl),
        );
    }

    /**
     * @dataProvider getRepositoriesInternalDataProvider
     * @param string $repositoryId
     * @param \PHPUnit_Framework_MockObject_MockObject $repositoryUrlCacheMock
     */
    public function testGetRepositoriesInternalReturnsArrayOfRepositoryInfos($repositoryId, \PHPUnit_Framework_MockObject_MockObject $repositoryUrlCacheMock)
    {
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertRepositoryInfo')
        )->getMock();

        $repositoryInfoBrowserBinding = new RepositoryInfoBrowserBinding();
        $repositoryInfoBrowserBinding->setId('id');
        $repositoryInfoBrowserBinding->setRepositoryUrl(self::BROWSER_URL_TEST);
        $repositoryInfoBrowserBinding->setRootUrl(self::BROWSER_URL_TEST);

        $jsonConverterMock->expects($this->once())->method('convertRepositoryInfo')->willReturn(
            $repositoryInfoBrowserBinding
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn(array(array('valid repository info stuff')));

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock(), $cmisBindingsHelperMock))->setMethods(
            array('getRepositoryUrlCache', 'getServiceUrl', 'read')
        )->getMockForAbstractClass();

        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->any())->method('getServiceUrl')->willReturn(self::BROWSER_URL_TEST);
        $binding->expects($this->any())->method('read')->willReturn($responseMock);

        $this->assertEquals(
            array($repositoryInfoBrowserBinding),
            $this->getMethod(self::CLASS_TO_TEST, 'getRepositoriesInternal')->invokeArgs($binding, array($repositoryId))
        );
    }

    public function getRepositoriesInternalDataProvider()
    {
        $mockBuilder = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryUrlCache'
        )->setMethods(array('getRepositoryUrl', 'buildUrl', 'addRepository'))->disableProxyingToOriginalMethods();

        $repositoryUrlCacheMockNoId = $mockBuilder->getMock();
        $repositoryUrlCacheMockNoId->expects($this->any())->method('buildUrl')->willReturn(
            Url::createFromUrl(self::BROWSER_URL_TEST)
        );

        $repositoryUrlCacheMock = $mockBuilder->getMock();
        $repositoryUrlCacheMock->expects($this->any())->method('buildUrl')->willReturn(
            Url::createFromUrl(self::BROWSER_URL_TEST)
        );

        $repositoryUrlCacheMockWithRepositoryUrlEntry = $mockBuilder->getMock();
        $repositoryUrlCacheMockWithRepositoryUrlEntry->expects($this->any())->method('getRepositoryUrl')->willReturn(
            Url::createFromUrl(self::BROWSER_URL_TEST)
        );
        $repositoryUrlCacheMockWithRepositoryUrlEntry->expects($this->once())->method('addRepository');

        return array(
            'no repository id - repository url cache builds url' => array(null, $repositoryUrlCacheMockNoId),
            'with repository id - repository url cache does NOT return repository url - url is build' => array(
                'repository-id',
                $repositoryUrlCacheMock
            ),
            'with repository id - repository url cache does return repository url - url is fetched from cache' => array(
                'repository-id',
                $repositoryUrlCacheMockWithRepositoryUrlEntry
            )
        );
    }

    public function testConvertPropertiesToQueryArrayConvertsPropertiesIntoAnArray()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $properties = new Properties();

        $currentTime = new \DateTime('now');

        $singleValueStringProperty = new PropertyString('stringProp', 'stringValue');

        $multiValueStringProperty = new PropertyString('stringProp2', array('stringValue1', 'stringValue2'));

        $singleValueBooleanProperty = new PropertyBoolean('booleanProp', true);

        $singleValueDecimalProperty = new PropertyDecimal('decimalProp', 1.2);

        $singleValueDateTimeProperty = new PropertyDateTime('dateTimeProp', $currentTime);

        $singleValueIdProperty = new PropertyId('idProp', 'idValue');

        $properties->addProperties(
            array(
                $singleValueStringProperty,
                $multiValueStringProperty,
                $singleValueBooleanProperty,
                $singleValueDecimalProperty,
                $singleValueDateTimeProperty,
                $singleValueIdProperty
            )
        );

        $expectedArray = array(
            Constants::CONTROL_PROP_ID => array(
                0 => 'stringProp',
                1 => 'stringProp2',
                2 => 'booleanProp',
                3 => 'decimalProp',
                4 => 'dateTimeProp',
                5 => 'idProp'
            ),
            Constants::CONTROL_PROP_VALUE => array(
                0 => 'stringValue',
                1 => array(
                    0 => 'stringValue1',
                    1 => 'stringValue2'
                ),
                2 => 'true',
                3 => 1.2,
                4 => $currentTime->getTimestamp() * 1000,
                5 => 'idValue'
            )
        );

        $this->assertEquals(
            $expectedArray,
            $this->getMethod(self::CLASS_TO_TEST, 'convertPropertiesToQueryArray')->invokeArgs(
                $binding,
                array($properties)
            )
        );
    }

    public function testConvertAclToQueryArrayConvertsAclIntoAnArrayForRemovingAcls()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $principal1 = new Principal('principalId1');
        $ace1 = new AccessControlEntry($principal1, array('permissionValue1', 'permissionValue2'));

        $principal2 = new Principal('principalId2');
        $ace2 = new AccessControlEntry($principal2, array('permissionValue3', 'permissionValue4'));
        $acl = new AccessControlList(array($ace1, $ace2));

        $expectedArray = array(
            'removeACEPrincipal' => array(
                0 => 'principalId1',
                1 => 'principalId2'
            ),
            'removeACEPermission' => array(
                0 => array(
                    0 => 'permissionValue1',
                    1 => 'permissionValue2'
                ),
                1 => array(
                    0 => 'permissionValue3',
                    1 => 'permissionValue4'
                )
            )
        );

        $this->assertEquals(
            $expectedArray,
            $this->getMethod(self::CLASS_TO_TEST, 'convertAclToQueryArray')->invokeArgs(
                $binding,
                array($acl, Constants::CONTROL_REMOVE_ACE_PRINCIPAL, Constants::CONTROL_REMOVE_ACE_PERMISSION)
            )
        );
    }

    public function testConvertAclToQueryArrayConvertsAclIntoAnArrayForAddingAcls()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $principal1 = new Principal('principalId1');
        $ace1 = new AccessControlEntry($principal1, array('permissionValue1', 'permissionValue2'));

        $principal2 = new Principal('principalId2');
        $ace2 = new AccessControlEntry($principal2, array('permissionValue3', 'permissionValue4'));
        $acl = new AccessControlList(array($ace1, $ace2));

        $expectedArray = array(
            'addACEPrincipal' => array(
                0 => 'principalId1',
                1 => 'principalId2'
            ),
            'addACEPermission' => array(
                0 => array(
                    0 => 'permissionValue1',
                    1 => 'permissionValue2'
                ),
                1 => array(
                    0 => 'permissionValue3',
                    1 => 'permissionValue4'
                )
            )
        );

        $this->assertEquals(
            $expectedArray,
            $this->getMethod(self::CLASS_TO_TEST, 'convertAclToQueryArray')->invokeArgs(
                $binding,
                array($acl, Constants::CONTROL_ADD_ACE_PRINCIPAL, Constants::CONTROL_ADD_ACE_PERMISSION)
            )
        );
    }

    public function testConvertPolicyIdArrayToQueryArrayConvertsPoliciesArrayIntoAnQueryArray()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $policies = array(
            'policyOne',
            'policyTwo'
        );

        $expectedArray = array(
            'policy' => array(
                0 => 'policyOne',
                1 => 'policyTwo'
            )
        );

        $this->assertEquals(
            $expectedArray,
            $this->getMethod(self::CLASS_TO_TEST, 'convertPolicyIdArrayToQueryArray')->invokeArgs(
                $binding,
                array($policies)
            )
        );
    }

    public function testGetDateTimeFormatReturnsPropertyValue()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertAttributeSame(
            $binding->getDateTimeFormat(),
            'dateTimeFormat',
            $binding
        );
    }

    public function testSetDateTimeFormatSetsProperty()
    {
        $sessionMock = $this->getSessionMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $dateTimeFormat = DateTimeFormat::cast(DateTimeFormat::EXTENDED);
        $binding->setDateTimeFormat($dateTimeFormat);
        $this->assertAttributeSame(
            $dateTimeFormat,
            'dateTimeFormat',
            $binding
        );
    }

    public function testConstructorSetsDateTimeFormatPropertyBasedOnSessionParameter()
    {
        $sessionMock = $this->getSessionMock();

        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertAttributeEquals(DateTimeFormat::cast(DateTimeFormat::SIMPLE), 'dateTimeFormat', $binding);

        $sessionMock = $this->getSessionMock(array(array(
            SessionParameter::BROWSER_DATETIME_FORMAT,
            null,
            DateTimeFormat::cast(DateTimeFormat::EXTENDED)
        )));

        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertAttributeEquals(DateTimeFormat::cast(DateTimeFormat::EXTENDED), 'dateTimeFormat', $binding);
    }

    public function testConstructorSetsDateTimeFormatPropertyBasedOnDefaultValue()
    {
        $sessionMock = $this->getSessionMock(array(array(SessionParameter::BROWSER_DATETIME_FORMAT, null, null)));

        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($sessionMock))->getMockForAbstractClass();

        $this->assertAttributeEquals(DateTimeFormat::cast(DateTimeFormat::SIMPLE), 'dateTimeFormat', $binding);
    }
}
