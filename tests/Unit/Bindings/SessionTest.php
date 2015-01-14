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

use Dkd\PhpCmis;
use Dkd\PhpCmis\Bindings\Session;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorCreatesUniqueSessionId()
    {
        $session = new Session();
        $this->assertNotEmpty($session->getSessionId());

        $session2 = new Session();
        $this->assertNotEquals($session->getSessionId(), $session2->getSessionId());
    }

    public function testGetSessionIdReturnsSessionId()
    {
        $session = new Session();
        $this->assertAttributeEquals($session->getSessionId(), 'sessionId', $session);
    }

    public function testPutAddsAValueToTheSession()
    {
        $session = new Session();
        $this->assertAttributeEquals(array(), 'data', $session);
        $session->put('foo', 'bar');
        $this->assertAttributeEquals(array('foo' => 'bar'), 'data', $session);

        return $session;
    }

    /**
     * @depends testPutAddsAValueToTheSession
     */
    public function testGetKeysReturnsAnArrayWithAllSessionDataKeys()
    {
        $session = new Session();
        $session->put('bar', 'foo');
        $session->put('baz', 'foofoo');
        $this->assertEquals(array('bar', 'baz'), $session->getKeys());
    }

    /**
     * @depends testPutAddsAValueToTheSession
     */
    public function testRemoveRemovesAValueFromTheSession()
    {
        $session = new Session();
        $session->put('bar1', 'foobar');
        $session->put('bar2', 'foobar');
        $session->put('bar3', 'foobar');
        $session->remove('bar3');
        $this->assertAttributeEquals(array('bar1' => 'foobar', 'bar2' => 'foobar'), 'data', $session);
        $session->remove('bar1');
        $this->assertAttributeEquals(array('bar2' => 'foobar'), 'data', $session);
    }

    /**
     * @depends testPutAddsAValueToTheSession
     * @dataProvider getFunctionDataProvider
     * @param string $key
     * @param mixed $defaultValue
     * @param mixed $expected
     */
    public function testGetReturnsSessionValueForAGivenKey($key, $defaultValue, $expected)
    {
        $session = new Session();
        $session->put('int-1', 1);
        $session->put('string-2', 'foo');
        $session->put('array-3', array('foobar'));

        $this->assertSame($expected, $session->get($key, $defaultValue));
    }

    public function getFunctionDataProvider()
    {
        return array(
            'int-1 returns an integer 1' => array(
                'int-1',
                null,
                1
            ),
            'the given default value is returned if the key does not exist' => array(
                'int-999',
                123,
                123
            ),
            'null is returned if the key does not exist and no default value is defined' => array(
                'int-999',
                null,
                null
            ),
            'string value is returned as string' => array(
                'string-2',
                null,
                'foo'
            ),
            'array value is returned as array' => array(
                'array-3',
                null,
                array('foobar')
            ),
        );
    }
}
