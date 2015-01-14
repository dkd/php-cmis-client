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

use Dkd\PhpCmis\DataObjects\ObjectId;

class ObjectIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidIdValuesDataProvider
     * @param mixed $idValue
     */
    public function testConstructorThrowsExceptionIfNoStringAsIdGiven($idValue)
    {
        $this->setExpectedException('\\InvalidArgumentException', 'Id must not be empty!');
        new ObjectId($idValue);
    }

    /**
     * Data provider with invalid id values
     *
     * @return array
     */
    public function invalidIdValuesDataProvider()
    {
        return array(
            array(''),
            array(null),
            array(0),
            array(1),
            array(array('foo')),
            array(new \stdClass())
        );
    }

    public function testConstructorSetsIdProperty()
    {
        $objectId = new ObjectId('foo');
        $this->assertAttributeSame('foo', 'id', $objectId);
    }

    public function testGetIdReturnsId()
    {
        $objectId = new ObjectId('foo');
        $this->assertSame('foo', $objectId->getId());
    }

    public function testToStringReturnsIdAsString()
    {
        $objectId = new ObjectId('foo');
        $this->assertSame('foo', (string) $objectId);
    }
}
