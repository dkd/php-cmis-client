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

use Dkd\PhpCmis\DataObjects\ObjectInFolderData;
use Dkd\PhpCmis\DataObjects\ObjectInFolderList;

class ObjectInFolderListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectInFolderList
     */
    protected $objectInFolderList;

    /**
     * @var ObjectInFolderData
     */
    protected $objectInFolderData;

    public function setUp()
    {
        $this->objectInFolderData = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Data\\ObjectInFolderDataInterface'
        )->disableOriginalConstructor()->getMockForAbstractClass();

        $this->objectInFolderList = new ObjectInFolderList(array($this->objectInFolderData));
    }

    public function testSetObjectsSetsProperty()
    {
        $objects = array($this->objectInFolderData);
        $this->objectInFolderList->setObjects($objects);
        $this->assertAttributeSame($objects, 'objects', $this->objectInFolderList);
    }

    public function testSetObjectsThrowsExceptionIfAGivenObjectIsNotOfTypeObjectInFolderDataInterface()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $this->objectInFolderList->setObjects(array(new \stdClass()));
    }

    /**
     * @depends testSetObjectsSetsProperty
     */
    public function testGetObjectsReturnsPropertyValue()
    {
        $objects = array($this->objectInFolderData);
        $this->objectInFolderList->setObjects($objects);
        $this->assertSame($objects, $this->objectInFolderList->getObjects());
    }

    public function testSetHasMoreItemsSetsHasMoreItems()
    {
        $this->objectInFolderList->setHasMoreItems(true);
        $this->assertAttributeSame(true, 'hasMoreItems', $this->objectInFolderList);
        $this->objectInFolderList->setHasMoreItems(false);
        $this->assertAttributeSame(false, 'hasMoreItems', $this->objectInFolderList);
    }

    public function testSetHasMoreItemsCastsValueToBoolean()
    {
        $this->setExpectedException('\\PHPUnit_Framework_Error_Notice');
        $this->objectInFolderList->setHasMoreItems(1);
        $this->assertAttributeSame(true, 'hasMoreItems', $this->objectInFolderList);
    }

    /**
     * @depends testSetHasMoreItemsSetsHasMoreItems
     */
    public function testHasMoreItemsReturnsHasMoreItems()
    {
        $this->objectInFolderList->setHasMoreItems(true);
        $this->assertTrue($this->objectInFolderList->hasMoreItems());
        $this->objectInFolderList->setHasMoreItems(false);
        $this->assertFalse($this->objectInFolderList->hasMoreItems());
    }

    public function testSetNumItemsSetsNumItems()
    {
        $this->objectInFolderList->setNumItems(2);
        $this->assertAttributeSame(2, 'numItems', $this->objectInFolderList);
        $this->objectInFolderList->setNumItems(3);
        $this->assertAttributeSame(3, 'numItems', $this->objectInFolderList);
    }

    /**
     * @depends testSetNumItemsSetsNumItems
     */
    public function testNumItemsReturnsNumItems()
    {
        $this->objectInFolderList->setNumItems(2);
        $this->assertEquals(2, $this->objectInFolderList->getNumItems());
        $this->objectInFolderList->setNumItems(3);
        $this->assertEquals(3, $this->objectInFolderList->getNumItems());
    }
}
