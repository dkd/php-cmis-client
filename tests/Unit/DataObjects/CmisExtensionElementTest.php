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
        $this->setExpectedException('\InvalidArgumentException', 'Name must be given!');
        new CmisExtensionElement('namespace', '');
    }

    public function testConstructorThrowsExceptionIfValueAndChildrenIsGiven()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'Value and children given! Only one of them is allowed.'
        );
        new CmisExtensionElement('namespace', 'name', null, 'value', array('children'));
    }

    public function testConstructorThrowsExceptionIfNoValueAndChildrenGiven()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'Value and children are empty! One of them is required.'
        );
        new CmisExtensionElement('namespace', 'name', null, null, null);
    }

    /**
     * @dataProvider stringCastDataProvider
     */
    public function testConstructorSetsNameAsProperty($expected, $value)
    {
        // filter empty values from the data provider because they will end in an exception here.
        if (!empty($value)) {
            $cmisExtensionElement = new CmisExtensionElement('namespace', $value, null, 'value', null);
            $this->assertAttributeSame($expected, 'name', $cmisExtensionElement);
        }
    }

    /**
     * @dataProvider stringCastDataProvider
     */
    public function testConstructorSetsNamespaceAsProperty($expected, $value)
    {
        $cmisExtensionElement = new CmisExtensionElement($value, 'name', null, 'value', null);
        $this->assertAttributeSame($expected, 'namespace', $cmisExtensionElement);
    }

    public function testConstructorSetsAttributesAsProperty()
    {
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', array('foo'), 'value');
        $this->assertAttributeSame(array('foo'), 'attributes', $cmisExtensionElement);
    }

    /**
     * @dataProvider stringCastDataProvider
     */
    public function testConstructorSetsValueAsProperty($expected, $value)
    {
        // filter empty values from the data provider because they will end in an exception here.
        if (!empty($value)) {
            $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', null, $value, null);
            $this->assertAttributeSame($expected, 'value', $cmisExtensionElement);
            $this->assertAttributeSame(array(), 'children', $cmisExtensionElement);
        }
    }

    public function testConstructorSetsChildrenAsProperty()
    {
        $children = array(new CmisExtensionElement('namespace', 'children', null, 'children'));
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', null, null, $children);
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
        $children = array(new CmisExtensionElement('namespace', 'children', null, 'children'));
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', null, null, $children);
        $this->assertEquals($children, $cmisExtensionElement->getChildren());
    }

    /**
     * @dependsOn testConstructorSetsNameAsProperty
     */
    public function testGetNameReturnsProperty()
    {
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', null, 'value');
        $this->assertEquals('name', $cmisExtensionElement->getName());
    }

    /**
     * @dependsOn testConstructorSetsNamespaceAsProperty
     */
    public function testGetNamespaceReturnsProperty()
    {
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', null, 'value');
        $this->assertEquals('namespace', $cmisExtensionElement->getNamespace());
    }

    /**
     * @dependsOn testConstructorSetsValueAsProperty
     */
    public function testGetValueReturnsProperty()
    {
        $cmisExtensionElement = new CmisExtensionElement('namespace', 'name', null, 'value');
        $this->assertEquals('value', $cmisExtensionElement->getValue());
    }
}
