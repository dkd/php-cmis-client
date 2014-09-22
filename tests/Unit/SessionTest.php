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
use Dkd\PhpCmis\ObjectFactoryInterface;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorThrowsExceptionIfNoParametersGiven()
    {
        $this->setExpectedException('\\InvalidArgumentException', 'No parameters provided!', 1408115280);
        new PhpCmis\Session(array());
    }

    public function testObjectFactoryIsSetToDefaultObjectFactoryWhenNoObjectFactoryIsGivenOrDefined()
    {
        $session = new PhpCmis\Session(array('foo'));
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\ObjectFactory', $session->getObjectFactory());
    }

    public function testObjectFactoryIsSetToObjectFactoryInstanceGivenAsMethodParameter()
    {
        /** @var ObjectFactoryInterface $dummyObjectFactory */
        $dummyObjectFactory = $this->getMock('\\Dkd\\PhpCmis\\ObjectFactoryInterface');
        $session = new PhpCmis\Session(array('foo'), $dummyObjectFactory);
        $this->assertSame($dummyObjectFactory, $session->getObjectFactory());
    }

    public function testObjectFactoryIsSetToObjectFactoryDefinedInParametersArray()
    {
        $objectFactory = $this->getMock('\\Dkd\\PhpCmis\\ObjectFactory');
        $session = new PhpCmis\Session(
            array(PhpCmis\SessionParameter::OBJECT_FACTORY_CLASS => get_class($objectFactory))
        );
        $this->assertEquals($objectFactory, $session->getObjectFactory());
    }

    public function testExceptionIsThrownIfConfiguredObjectFactoryDoesNotImplementObjectFactoryInterface()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException',
            null,
            1408354120
        );

        $object = $this->getMock('\\stdClass');
        new PhpCmis\Session(
            array(PhpCmis\SessionParameter::OBJECT_FACTORY_CLASS => get_class($object))
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
        $constructor->invoke($mock, array('foo'));
    }


    public function testCacheIsSetToDefaultCacheWhenNoCacheIsGivenOrDefined()
    {
        $session = new PhpCmis\Session(array('foo'));
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\Cache', $session->getCache());
    }

    public function testCacheIsSetToCacheInstanceGivenAsMethodParameter()
    {
        /** @var \Dkd\PhpCmis\CacheInterface $dummyCache */
        $dummyCache = $this->getMock('\\Dkd\\PhpCmis\\Cache');
        $session = new PhpCmis\Session(array('foo'), null, null, $dummyCache);
        $this->assertSame($dummyCache, $session->getCache());
    }

    public function testCacheIsSetToCacheDefinedInParametersArray()
    {
        $cache = $this->getMock('\\Dkd\\PhpCmis\\Cache');
        $session = new PhpCmis\Session(
            array(PhpCmis\SessionParameter::CACHE_CLASS => get_class($cache))
        );
        $this->assertEquals($cache, $session->getCache());
    }

    public function testExceptionIsThrownIfConfiguredCacheDoesNotImplementCacheInterface()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException',
            null,
            1408354123
        );

        $object = $this->getMock('\\stdClass');
        new PhpCmis\Session(
            array(PhpCmis\SessionParameter::CACHE_CLASS => get_class($object))
        );
    }

    public function testCreatedCacheInstanceWillBeInitialized()
    {
        // dummy cache with a spy on initialize
        $cache = $this->getMock('\\Dkd\\PhpCmis\\Cache');
        $cache->expects($this->once())->method('initialize');

        $sessionClassName = '\\Dkd\\PhpCmis\\Session';

        // Get mock, without the constructor being called
        $mock = $this->getMockBuilder($sessionClassName)
            ->disableOriginalConstructor()
            ->setMethods(array('createDefaultCacheInstance'))
            ->getMock();

        // set createDefaultCacheInstance to return our cache spy
        $mock->expects($this->once())
            ->method('createDefaultCacheInstance')
            ->willReturn($cache);

        // now call the constructor
        $reflectedClass = new \ReflectionClass(get_class($mock));
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($mock, array('foo'));
    }
}
