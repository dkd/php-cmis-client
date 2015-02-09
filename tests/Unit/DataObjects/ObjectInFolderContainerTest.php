<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ObjectInFolderContainerInterface;
use Dkd\PhpCmis\DataObjects\ObjectInFolderData;
use Dkd\PhpCmis\DataObjects\ObjectInFolderContainer;

class ObjectInFolderContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectInFolderContainer
     */
    protected $objectInFolderContainer;

    public function setUp()
    {
        $this->objectInFolderContainer = new ObjectInFolderContainer(new ObjectInFolderData());
    }

    public function testSetObjectSetsProperty()
    {
        $objectData = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ObjectInFolderDataInterface');
        $this->objectInFolderContainer->setObject($objectData);
        $this->assertAttributeSame($objectData, 'object', $this->objectInFolderContainer);
    }

    /**
     * @depends testSetObjectSetsProperty
     */
    public function testGetObjectReturnsPropertyValue()
    {
        $objectData = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ObjectInFolderDataInterface');
        $this->objectInFolderContainer->setObject($objectData);
        $this->assertSame($objectData, $this->objectInFolderContainer->getObject());
    }

    public function testSetObjectsSetsProperty()
    {
        $children = array($this->getObjectInFolderContainerMock());

        $this->objectInFolderContainer->setChildren($children);
        $this->assertAttributeSame($children, 'children', $this->objectInFolderContainer);
    }

    public function testSetChildrenThrowsExceptionIfAGivenObjectIsNotOfTypeObjectInFolderContainerInterface()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $this->objectInFolderContainer->setChildren(array(new \stdClass()));
    }

    /**
     * @depends testSetObjectsSetsProperty
     */
    public function testGetObjectsReturnsPropertyValue()
    {
        $children = array($this->getObjectInFolderContainerMock());

        $this->objectInFolderContainer->setChildren($children);
        $this->assertSame($children, $this->objectInFolderContainer->getChildren());
    }

    public function testConstructorSetsObject()
    {
        $expectedObject = new ObjectInFolderData;
        $objectInFolderContainer = new ObjectInFolderContainer($expectedObject);
        $this->assertAttributeSame($expectedObject, 'object', $objectInFolderContainer);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ObjectInFolderContainerInterface
     */
    protected function getObjectInFolderContainerMock()
    {
        return $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Data\\ObjectInFolderContainerInterface'
        )->disableOriginalConstructor()->getMockForAbstractClass();
    }
}
