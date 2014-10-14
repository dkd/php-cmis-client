<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

/**
 * This file is part of php-cmis-client
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\DataObjects\AbstractPropertyData;
use PHPUnit_Framework_MockObject_MockObject;

class AbstractPropertyDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|AbstractPropertyData
     */
    protected $propertyDataMock;

    public function setUp()
    {
        $this->propertyDataMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\AbstractPropertyData'
        )->setConstructorArgs(
            array('foo', 'value')
        )->getMockForAbstractClass();
    }

    public function testConstructorSetsIdAndValueProperty()
    {
        $this->assertAttributeSame('foo', 'id', $this->propertyDataMock);
        $this->assertAttributeSame(array('value'), 'values', $this->propertyDataMock);


        $propertyData = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\AbstractPropertyData')->setConstructorArgs(
            array('foo', array('bar', 'value'))
        )->getMockForAbstractClass();
        $this->assertAttributeSame(array('bar', 'value'), 'values', $propertyData);
    }

    public function testSetDisplayNameSetsProperty()
    {
        $this->propertyDataMock->setDisplayName('displayName');
        $this->assertAttributeSame('displayName', 'displayName', $this->propertyDataMock);
    }

    /**
     * @depends testSetDisplayNameSetsProperty
     */
    public function testGetDisplayNameGetsProperty()
    {
        $this->propertyDataMock->setDisplayName('displayName');
        $this->assertSame('displayName', $this->propertyDataMock->getDisplayName());
    }

    public function testSetIdSetsProperty()
    {
        $this->propertyDataMock->setId('id');
        $this->assertAttributeSame('id', 'id', $this->propertyDataMock);
    }

    /**
     * @depends testSetIdSetsProperty
     */
    public function testGetIdGetsProperty()
    {
        $this->propertyDataMock->setId('id');
        $this->assertSame('id', $this->propertyDataMock->getId());
    }

    public function testSetLocalNameSetsProperty()
    {
        $this->propertyDataMock->setLocalName('localName');
        $this->assertAttributeSame('localName', 'localName', $this->propertyDataMock);
    }

    /**
     * @depends testSetLocalNameSetsProperty
     */
    public function testGetLocalNameGetsProperty()
    {
        $this->propertyDataMock->setLocalName('localName');
        $this->assertSame('localName', $this->propertyDataMock->getLocalName());
    }

    public function testSetQueryNameSetsProperty()
    {
        $this->propertyDataMock->setQueryName('queryName');
        $this->assertAttributeSame('queryName', 'queryName', $this->propertyDataMock);
    }

    /**
     * @depends testSetQueryNameSetsProperty
     */
    public function testGetQueryNameGetsProperty()
    {
        $this->propertyDataMock->setQueryName('queryName');
        $this->assertSame('queryName', $this->propertyDataMock->getQueryName());
    }

    public function testSetValuesSetsProperty()
    {
        $this->propertyDataMock->setValues(array('value'));
        $this->assertAttributeSame(array('value'), 'values', $this->propertyDataMock);
    }

    public function testSetValuesSetsPropertyAsIndexedArray()
    {
        $values = array(0 => 'foo', 'a' => 'bar', 'baz');
        $this->propertyDataMock->setValues($values);
        $this->assertAttributeSame(array(0 => 'foo', 1 => 'bar', 2 => 'baz'), 'values', $this->propertyDataMock);
    }

    /**
     * @depends testSetValuesSetsProperty
     */
    public function testGetValuesGetsProperty()
    {
        $this->propertyDataMock->setValues(array('value'));
        $this->assertSame(array('value'), $this->propertyDataMock->getValues());
    }

    public function testSetValueSetsSingleValueAsArrayInProperty()
    {
        $this->propertyDataMock->setValue('value');
        $this->assertAttributeSame(array('value'), 'values', $this->propertyDataMock);
    }

    /**
     * @depends testSetValuesSetsProperty
     */
    public function testGetFirstValueReturnsFirstEntryOfValuesProperty()
    {
        $this->propertyDataMock->setValues(array('value1', 'value2', 'value3'));
        $this->assertSame('value1', $this->propertyDataMock->getFirstValue());
    }

    /**
     * @depends testSetValuesSetsProperty
     */
    public function testGetFirstValueReturnsNullIfPropertyDoesNotContainValues()
    {
        $this->propertyDataMock->setValues(array());
        $this->assertNull($this->propertyDataMock->getFirstValue());
    }
}
