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
use Dkd\PhpCmis\Bindings\Browser\AbstractBrowserBindingService;
use Dkd\PhpCmis\Bindings\Browser\RepositoryUrlCache;
use Dkd\PhpCmis\Bindings\CmisBindingsHelper;
use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\Converter\JsonConverter;
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
use Dkd\PhpCmis\Exception\CmisConnectionException;
use Dkd\PhpCmis\Exception\CmisConstraintException;
use Dkd\PhpCmis\Exception\CmisContentAlreadyExistsException;
use Dkd\PhpCmis\Exception\CmisFilterNotValidException;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\Exception\CmisNameConstraintViolationException;
use Dkd\PhpCmis\Exception\CmisNotSupportedException;
use Dkd\PhpCmis\Exception\CmisObjectNotFoundException;
use Dkd\PhpCmis\Exception\CmisPermissionDeniedException;
use Dkd\PhpCmis\Exception\CmisProxyAuthenticationException;
use Dkd\PhpCmis\Exception\CmisRuntimeException;
use Dkd\PhpCmis\Exception\CmisStorageException;
use Dkd\PhpCmis\Exception\CmisStreamNotSupportedException;
use Dkd\PhpCmis\Exception\CmisUnauthorizedException;
use Dkd\PhpCmis\Exception\CmisUpdateConflictException;
use Dkd\PhpCmis\Exception\CmisVersioningException;
use Dkd\PhpCmis\SessionParameter;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use League\Url\Url;
use PHPUnit_Framework_MockObject_MockObject;

class AbstractBrowserBindingServiceTest extends AbstractBrowserBindingServiceTestCase
{
    const CLASS_TO_TEST = AbstractBrowserBindingService::class;

    public function testConstructorSetsSessionAndBindingsHelper()
    {
        $sessionMock = $this->getMockBuilder(
            BindingSessionInterface::class
        )->setMockClassName('SessionMock')->getMockForAbstractClass();

        $bindingHelper = $this->getMockBuilder(CmisBindingsHelper::class)->setMockClassName(
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
        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
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
            RepositoryUrlCache::class
        )->setMethods(array('getRepositoryUrl'))->getMock();

        $repositoryUrlCacheMock->expects($this->exactly(2))->method('getRepositoryUrl')->willReturn(null);
        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->once())->method('getRepositoriesInternal');

        $this->setExpectedException(CmisObjectNotFoundException::class);

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
            RepositoryUrlCache::class
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
            RepositoryUrlCache::class
        )->setMethods(array('getObjectUrl'))->getMock();

        $repositoryUrlCacheMock->expects($this->exactly(2))->method('getObjectUrl')->willReturn(null);
        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->once())->method('getRepositoriesInternal');

        $this->setExpectedException(CmisObjectNotFoundException::class);

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
            RepositoryUrlCache::class
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
            RepositoryUrlCache::class
        )->setMethods(array('getPathUrl'))->getMock();

        $repositoryUrlCacheMock->expects($this->exactly(2))->method('getPathUrl')->willReturn(null);
        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->once())->method('getRepositoriesInternal');

        $this->setExpectedException(CmisObjectNotFoundException::class);

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
            RepositoryUrlCache::class
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
                CmisConstraintException::class,
                0,
                '{"message":"Error message","exception":"constraint"}'
            ),
            array(
                CmisContentAlreadyExistsException::class,
                0,
                '{"message":"Error message","exception":"contentAlreadyExists"}'
            ),
            array(
                CmisFilterNotValidException::class,
                0,
                '{"message":"Error message","exception":"filterNotValid"}'
            ),
            array(
                CmisInvalidArgumentException::class,
                0,
                '{"message":"Error message","exception":"invalidArgument"}'
            ),
            array(
                CmisNameConstraintViolationException::class,
                0,
                '{"message":"Error message","exception":"nameConstraintViolation"}'
            ),
            array(
                CmisNotSupportedException::class,
                0,
                '{"message":"Error message","exception":"notSupported"}'
            ),
            array(
                CmisObjectNotFoundException::class,
                0,
                '{"message":"Error message","exception":"objectNotFound"}'
            ),
            array(
                CmisPermissionDeniedException::class,
                0,
                '{"message":"Error message","exception":"permissionDenied"}'
            ),
            array(
                CmisStorageException::class,
                0,
                '{"message":"Error message","exception":"storage"}'
            ),
            array(
                CmisStreamNotSupportedException::class,
                0,
                '{"message":"Error message","exception":"streamNotSupported"}'
            ),
            array(
                CmisUpdateConflictException::class,
                0,
                '{"message":"Error message","exception":"updateConflict"}'
            ),
            array(
                CmisVersioningException::class,
                0,
                '{"message":"Error message","exception":"versioning"}'
            ),
            // unknown exception name
            array(
                CmisRuntimeException::class,
                399,
                '{"message":"Error message","exception":"unknownExceptionNameFooBar"}'
            ),
            // bases on status code
            array(
                CmisConnectionException::class,
                301,
                'error message'
            ),
            array(
                CmisConnectionException::class,
                302,
                'error message'
            ),
            array(
                CmisConnectionException::class,
                303,
                'error message'
            ),
            array(
                CmisConnectionException::class,
                307,
                'error message'
            ),
            array(
                CmisInvalidArgumentException::class,
                400,
                'error message'
            ),
            array(
                CmisUnauthorizedException::class,
                401,
                'error message'
            ),
            array(
                CmisPermissionDeniedException::class,
                403,
                'error message'
            ),
            array(
                CmisObjectNotFoundException::class,
                404,
                'error message'
            ),
            array(
                CmisNotSupportedException::class,
                405,
                'error message'
            ),
            array(
                CmisProxyAuthenticationException::class,
                407,
                'error message'
            ),
            array(
                CmisConstraintException::class,
                409,
                'error message'
            ),
            array(
                CmisRuntimeException::class,
                500,
                'error message'
            ),
            array(
                CmisRuntimeException::class,
                9999,
                'error message'
            ),
        );
    }

    public function testReadCallsHttpInvokerAndReturnsRequestResult()
    {
        $sessionMock = $this->getSessionMock();

        $testUrl = Url::createFromUrl(self::BROWSER_URL_TEST);
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $httpInvokerMock = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->setMethods(
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
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock(
        );
        $httpInvokerMock = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->setMethods(
            array('get')
        )->getMock();
        /** @var RequestException|PHPUnit_Framework_MockObject_MockObject $exceptionMock */
        $exceptionMock = $this->getMockBuilder(RequestException::class)->disableOriginalConstructor()
            ->setMethods(array('getResponse'))->getMock();
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
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock(
        );
        $httpInvokerMock = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->setMethods(
            array('get')
        )->getMock();
        /** @var RequestException|PHPUnit_Framework_MockObject_MockObject $exceptionMock */
        $exceptionMock = new RequestException('foobar', new Request('GET', static::BROWSER_URL_TEST), $responseMock);
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

        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $httpInvokerMock = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->setMethods(
            array('post')
        )->getMock();
        $httpInvokerMock->expects($this->once())->method('post')->with(
            $testUrl,
            array('form_params' => $content)
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
        $testUrl = Url::createFromUrl('http://foo.bar.baz');
        $content = 'fooBarBaz';

        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock(
        );
        $httpInvokerMock = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->setMethods(
            array('post')
        )->getMock();
        /** @var RequestException|PHPUnit_Framework_MockObject_MockObject $exceptionMock */
        $exceptionMock = $this->getMockBuilder(RequestException::class)->disableOriginalConstructor(
        )->setMethods(array('getResponse'))->getMock();
        $exceptionMock->expects($this->any())->method('getResponse')->willReturn($responseMock);
        $httpInvokerMock->expects($this->once())->method('post')->with(
            (string) $testUrl,
            array('form_params' => $content)
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
            RepositoryUrlCache::class,
            $this->getMethod(self::CLASS_TO_TEST, 'getRepositoryUrlCache')->invoke($binding)
        );
    }

    public function testGetTypeDefinitionInternalBuildsAndReadsUrlAndConvertsJsonResultToTypeDefinitionObject()
    {
        $repositoryId = 'repositoryId';
        $typeId = 'typeId';
        $dummyResponse = new Response(200, [], '{"foo": "bar"}');

        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            array('convertTypeDefinition')
        )->getMock();
        $jsonConverterMock->expects($this->once())->method('convertTypeDefinition')->willReturn('TypeDefinitionResult');
        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock(), $cmisBindingsHelperMock))->setMethods(
            array('getRepositoryUrl', 'read')
        )->getMockForAbstractClass();

        $urlDummy = Url::createFromUrl('http://foo.bar.baz?foo=bar');
        $expectedUrl = Url::createFromUrl('http://foo.bar.baz?foo=bar&typeId=typeId');

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

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock(), $cmisBindingsHelperMock))->getMockForAbstractClass();

        $this->setExpectedException(
            CmisInvalidArgumentException::class,
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

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock(), $cmisBindingsHelperMock))->getMockForAbstractClass();

        $this->setExpectedException(
            CmisInvalidArgumentException::class,
            'Type id must not be empty!'
        );
        $this->getMethod(self::CLASS_TO_TEST, 'getTypeDefinitionInternal')->invokeArgs(
            $binding,
            array($repositoryId, $typeId)
        );
    }

    public function testGetRepositoriesInternalThrowsExceptionIfRequestDoesNotReturnValidJson()
    {
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(array('getBody'))->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(null);

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock()))->setMethods(
            array('getRepositoryUrlCache', 'getServiceUrl', 'read')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            RepositoryUrlCache::class
        )->setMethods(array('buildUrl'))->getMock();
        $repositoryUrlCacheMock->expects($this->any())->method('buildUrl')->willReturn(
            Url::createFromUrl(self::BROWSER_URL_TEST)
        );

        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->any())->method('getServiceUrl')->willReturn(self::BROWSER_URL_TEST);
        $binding->expects($this->any())->method('read')->willReturn($responseMock);

        $this->setExpectedException(CmisConnectionException::class, null, 1416343166);
        $this->getMethod(self::CLASS_TO_TEST, 'getRepositoriesInternal')->invoke($binding);
    }

    public function testGetRepositoriesInternalThrowsExceptionIfRequestDoesNotReturnAnItemArray()
    {
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(array('getBody'))->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn('[1]');

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock()))->setMethods(
            array('getRepositoryUrlCache', 'getServiceUrl', 'read')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            RepositoryUrlCache::class
        )->setMethods(array('buildUrl'))->getMock();
        $repositoryUrlCacheMock->expects($this->any())->method('buildUrl')->willReturn(
            Url::createFromUrl(self::BROWSER_URL_TEST)
        );

        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->any())->method('getServiceUrl')->willReturn(self::BROWSER_URL_TEST);
        $binding->expects($this->any())->method('read')->willReturn($responseMock);

        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisConnectionException', null, 1415187764);
        $this->getMethod(self::CLASS_TO_TEST, 'getRepositoriesInternal')->invoke($binding);
    }

    /**
     * @dataProvider incompleteRepositoryInfosDataProvider
     * @param $repositoryInfo
     */
    public function testGetRepositoriesInternalThrowsExceptionIfRepositoryInfosAreEmpty($repositoryInfo)
    {
        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            array('convertRepositoryInfo')
        )->getMock();

        $jsonConverterMock->expects($this->once())->method('convertRepositoryInfo')->willReturn(
            $repositoryInfo
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(array('getBody'))->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode(array(array('valid repository info stuff'))));

        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractBrowserBindingService $binding */
        $binding = $this->getMockBuilder(
            self::CLASS_TO_TEST
        )->setConstructorArgs(array($this->getSessionMock(), $cmisBindingsHelperMock))->setMethods(
            array('getRepositoryUrlCache', 'getServiceUrl', 'read')
        )->getMockForAbstractClass();

        $repositoryUrlCacheMock = $this->getMockBuilder(
            RepositoryUrlCache::class
        )->setMethods(array('buildUrl'))->getMock();
        $repositoryUrlCacheMock->expects($this->any())->method('buildUrl')->willReturn(
            Url::createFromUrl(self::BROWSER_URL_TEST)
        );

        $binding->expects($this->any())->method('getRepositoryUrlCache')->willReturn($repositoryUrlCacheMock);
        $binding->expects($this->any())->method('getServiceUrl')->willReturn(self::BROWSER_URL_TEST);
        $binding->expects($this->any())->method('read')->willReturn($responseMock);

        $this->setExpectedException(CmisConnectionException::class, null, 1415187765);
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
        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            array('convertRepositoryInfo')
        )->getMock();

        $repositoryInfoBrowserBinding = new RepositoryInfoBrowserBinding();
        $repositoryInfoBrowserBinding->setId('id');
        $repositoryInfoBrowserBinding->setRepositoryUrl(self::BROWSER_URL_TEST);
        $repositoryInfoBrowserBinding->setRootUrl(self::BROWSER_URL_TEST);

        $jsonConverterMock->expects($this->once())->method('convertRepositoryInfo')->willReturn(
            $repositoryInfoBrowserBinding
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(array('getBody'))->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn('[["some info"]]');

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
        $repositoryUrlCacheMock = $this->getMockBuilder(
            RepositoryUrlCache::class
        )->setMethods(array('getRepositoryUrl', 'buildUrl', 'addRepository'))->disableProxyingToOriginalMethods(
        )->getMock();
        $repositoryUrlCacheMock->expects($this->any())->method('buildUrl')->willReturn(
            Url::createFromUrl('http://foo.bar.baz')
        );
        $repositoryUrlCacheMockWithRepositoryUrlEntry = clone $repositoryUrlCacheMock;
        $repositoryUrlCacheMockWithRepositoryUrlEntry->expects($this->any())->method('getRepositoryUrl')->willReturn(
            Url::createFromUrl('http://foo.bar.baz')
        );
        $repositoryUrlCacheMockWithRepositoryUrlEntry->expects($this->once())->method('addRepository');

        return array(
            'no repository id - repository url cache builds url' => array(null, $repositoryUrlCacheMock),
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
