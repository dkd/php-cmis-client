<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\DataObjects\CmisExtensionElement;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class CmisExtensionElementTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    public function testConstructorThrowsExceptionIfNameIsEmpty()
    {
        $this->setExpectedException('\\InvalidArgumentException', 'Name must be given!');
        new CmisExtensionElement('namespace', '');
    }

    public function testConstructorThrowsExceptionIfValueAndChildrenIsGiven()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException',
            'Value and children given! Only one of them is allowed.'
        );
        new CmisExtensionElement('namespace', 'name', array(), 'value', array('children'));
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param string $expected
     * @param mixed $value
     */
    public function testConstructorSetsNameAsProperty($expected, $value)
    {
        // filter empty values from the data provider because they will end in an exception here.
        if (!empty($value)) {
            $cmisExtensionElement = new CmisExtensionElement('namespace', $value, array(), 'value');
            $this->assertAttributeSame($expected, 'name', $cmisExtensionElement);
        }
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param string $expected
     * @param mixed $value
     */
    public function testConstructorSetsNamespaceAsProperty($expected, $value)
    {
        $cmisExtensionElement = new CmisExtensionElement($value, 'name', array(), 'value');
        $this->assertAttributeSame($expected, 'namespace', $cmisExtensionElement);
    }

    public function testConstructorSetsAttributesAsProperty()
    {
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', array('foo'), 'value');
        $this->assertAttributeSame(array('foo'), 'attributes', $cmisExtensionElement);
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param string $expected
     * @param mixed $value
     */
    public function testConstructorSetsValueAsProperty($expected, $value)
    {
        // filter empty values from the data provider because they will end in an exception here.
        if (!empty($value)) {
            $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', array(), $value);
            $this->assertAttributeSame($expected, 'value', $cmisExtensionElement);
            $this->assertAttributeSame(array(), 'children', $cmisExtensionElement);
        }
    }

    public function testConstructorSetsChildrenAsProperty()
    {
        $children = array(new CmisExtensionElement('namespace', 'children', array(), 'children'));
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', array(), null, $children);
        $this->assertAttributeSame(null, 'value', $cmisExtensionElement);
        $this->assertAttributeSame($children, 'children', $cmisExtensionElement);
    }

    /**
     * @dependsOn testConstructorSetsAttributesAsProperty
     */
    public function testGetAttributesReturnsProperty()
    {
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', array('foo'), 'value');
        $this->assertEquals(array('foo'), $cmisExtensionElement->getAttributes());
    }

    /**
     * @dependsOn testConstructorSetsChildrenAsProperty
     */
    public function testGetChildrenReturnsProperty()
    {
        $children = array(new CmisExtensionElement('namespace', 'children', array(), 'children'));
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', array(), null, $children);
        $this->assertEquals($children, $cmisExtensionElement->getChildren());
    }

    /**
     * @dependsOn testConstructorSetsNameAsProperty
     */
    public function testGetNameReturnsProperty()
    {
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', array(), 'value');
        $this->assertEquals('name', $cmisExtensionElement->getName());
    }

    /**
     * @dependsOn testConstructorSetsNamespaceAsProperty
     */
    public function testGetNamespaceReturnsProperty()
    {
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', array(), 'value');
        $this->assertEquals('namespace', $cmisExtensionElement->getNamespace());
    }

    /**
     * @dependsOn testConstructorSetsValueAsProperty
     */
    public function testGetValueReturnsProperty()
    {
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', array(), 'value');
        $this->assertEquals('value', $cmisExtensionElement->getValue());
    }
}
