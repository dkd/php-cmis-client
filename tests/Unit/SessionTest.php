<?php
namespace Dkd\PhpCmis\Test\Unit;

/**
 * This file is part of php-cmis-client
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis;
use Dkd\PhpCmis\Bindings\CmisBindingsHelper;
use Dkd\PhpCmis\ObjectFactoryInterface;
use Dkd\PhpCmis\Session;
use Dkd\PhpCmis\SessionParameter;
use PHPUnit_Framework_MockObject_MockObject;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorThrowsExceptionIfNoParametersGiven()
    {
        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
            'No parameters provided!',
            1408115280
        );
        new Session(array());
    }

    /**
     * @return CmisBindingsHelper|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getBindingsHelperMock()
    {
        $repositoryServiceMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\RepositoryServiceInterface'
        )->getMockForAbstractClass();
        $relationshipServiceMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\RelationshipServiceInterface'
        )->getMockForAbstractClass();
        $bindingMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingInterface')->setMethods(
            array('getRepositoryService', 'getRelationshipService')
        )->getMockForAbstractClass();
        $bindingMock->expects($this->any())->method('getRepositoryService')->willReturn($repositoryServiceMock);
        $bindingMock->expects($this->any())->method('getRelationshipService')->willReturn($relationshipServiceMock);
        /** @var CmisBindingsHelper|PHPUnit_Framework_MockObject_MockObject $bindingsHelperMock */
        $bindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('createBinding')
        )->getMockForAbstractClass();
        $bindingsHelperMock->expects($this->any())->method('createBinding')->willReturn($bindingMock);

        return $bindingsHelperMock;
    }

    public function testObjectFactoryIsSetToDefaultObjectFactoryWhenNoObjectFactoryIsGivenOrDefined()
    {
        $session = new Session(
            array(SessionParameter::REPOSITORY_ID => 'foo'),
            null,
            null,
            null,
            null,
            $this->getBindingsHelperMock()
        );
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\ObjectFactory', $session->getObjectFactory());
    }

    public function testObjectFactoryIsSetToObjectFactoryInstanceGivenAsMethodParameter()
    {
        /** @var ObjectFactoryInterface $dummyObjectFactory */
        $dummyObjectFactory = $this->getMock('\\Dkd\\PhpCmis\\ObjectFactoryInterface');
        $session = new Session(
            array(SessionParameter::REPOSITORY_ID => 'foo'),
            $dummyObjectFactory,
            null,
            null,
            null,
            $this->getBindingsHelperMock()
        );

        $this->assertSame($dummyObjectFactory, $session->getObjectFactory());
    }

    public function testObjectFactoryIsSetToObjectFactoryDefinedInParametersArray()
    {
        $objectFactory = $this->getMock('\\Dkd\\PhpCmis\\ObjectFactory');
        $session = new Session(
            array(
                SessionParameter::REPOSITORY_ID => 'foo',
                SessionParameter::OBJECT_FACTORY_CLASS => get_class($objectFactory)
            ),
            null,
            null,
            null,
            null,
            $this->getBindingsHelperMock()
        );

        $this->assertEquals($objectFactory, $session->getObjectFactory());
    }

    public function testExceptionIsThrownIfConfiguredObjectFactoryDoesNotImplementObjectFactoryInterface()
    {
        $this->setExpectedException(
            '\\RuntimeException',
            '',
            1408354120
        );

        $object = $this->getMock('\\stdClass');
        new Session(
            array(SessionParameter::OBJECT_FACTORY_CLASS => get_class($object))
        );
    }

    public function testCreatedObjectFactoryInstanceWillBeInitialized()
    {
        // dummy object factory with a spy on initialize
        $objectFactory = $this->getMock('\\Dkd\\PhpCmis\\ObjectFactory');
        $objectFactory->expects($this->once())->method('initialize');

        $sessionClassName = '\\Dkd\\PhpCmis\\Session';

        // Get mock, without the constructor being called
        $mock = $this->getMockBuilder($sessionClassName)
                     ->disableOriginalConstructor()
                     ->setMethods(array('createDefaultObjectFactoryInstance'))
                     ->getMock();

        // set createDefaultObjectFactoryInstance to return our object factory spy
        $mock->expects($this->once())
             ->method('createDefaultObjectFactoryInstance')
             ->willReturn($objectFactory);

        // now call the constructor
        $reflectedClass = new \ReflectionClass(get_class($mock));
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke(
            $mock,
            array(SessionParameter::REPOSITORY_ID => 'foo'),
            null,
            null,
            null,
            null,
            $this->getBindingsHelperMock()
        );
    }

    public function testCreateQueryStatementThrowsErrorOnEmptyProperties()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $mock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Session')
            ->setMethods(array('dummy'))
            ->disableOriginalConstructor()
            ->getMock();
        $mock->createQueryStatement(array(), array('foobar'));
    }

    public function testCreateQueryStatementThrowsErrorOnEmptyTypes()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $mock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Session')
            ->setMethods(array('dummy'))
            ->disableOriginalConstructor()
            ->getMock();
        $mock->createQueryStatement(array('foobar'), array());
    }

    public function testCacheIsSetToDefaultCacheWhenNoCacheIsGivenOrDefined()
    {
        $session = new Session(
            array(SessionParameter::REPOSITORY_ID => 'foo'),
            null,
            null,
            null,
            null,
            $this->getBindingsHelperMock()
        );
        $this->assertInstanceOf('\\Doctrine\\Common\\Cache\\Cache', $session->getCache());
    }

    public function testCacheIsSetToCacheInstanceGivenAsMethodParameter()
    {
        /** @var \Doctrine\Common\Cache\Cache $dummyCache */
        $dummyCache = $this->getMockForAbstractClass('\\Doctrine\\Common\\Cache\\Cache');
        $session = new Session(
            array(SessionParameter::REPOSITORY_ID => 'foo'),
            null,
            $dummyCache,
            null,
            null,
            $this->getBindingsHelperMock()
        );
        $this->assertSame($dummyCache, $session->getCache());
    }

    public function testCacheIsSetToCacheDefinedInParametersArray()
    {
        /** @var \Doctrine\Common\Cache\Cache $dummyCache */
        $cache = $this->getMockForAbstractClass('\\Doctrine\\Common\\Cache\\CacheProvider');
        $session = new Session(
            array(SessionParameter::REPOSITORY_ID => 'foo', SessionParameter::CACHE_CLASS => get_class($cache)),
            null,
            null,
            null,
            null,
            $this->getBindingsHelperMock()
        );
        $this->assertEquals($cache, $session->getCache());
    }

    public function testExceptionIsThrownIfConfiguredCacheDoesNotImplementCacheInterface()
    {
        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
            '',
            1408354123
        );

        $object = $this->getMock('\\stdClass');
        new Session(
            array(SessionParameter::CACHE_CLASS => get_class($object))
        );
    }

    public function testGetRelationships()
    {
        $bindingsMock = $this->getBindingsHelperMock();
        $bindingsMock->createBinding(array())->getRelationshipService()
            ->expects($this->once())
            ->method('getObjectRelationships')
            ->with();
        $session = new Session(
            array(SessionParameter::REPOSITORY_ID => 'foo'),
            null,
            null,
            null,
            null,
            $bindingsMock
        );
        $repositoryInfo = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\RepositoryInfo')
            ->setMethods(array('getId'))
            ->getMock();
        $repositoryInfo->expects($this->once())->method('getId');
        $objectType = $this->getMockBuilder('\\Dkd\\PhpCmis\\Data\\ObjectTypeInterface')
            ->setMethods(array('getId', '__toString'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $property = new \ReflectionProperty($session, 'repositoryInfo');
        $property->setAccessible(true);
        $property->setValue($session, $repositoryInfo);
        $session->getRelationships(
            new PhpCmis\DataObjects\ObjectId('foobar-object-id'),
            true,
            PhpCmis\Enum\RelationshipDirection::cast(PhpCmis\Enum\RelationshipDirection::TARGET),
            $objectType
        );
    }
}
