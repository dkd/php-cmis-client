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

class ObjectInFolderDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectInFolderData
     */
    protected $objectInFolderData;

    public function setUp()
    {
        $this->objectInFolderData = new ObjectInFolderData();
    }

    public function testSetObjectSetsProperty()
    {
        $objectData = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ObjectDataInterface');
        $this->objectInFolderData->setObject($objectData);
        $this->assertAttributeSame($objectData, 'object', $this->objectInFolderData);
    }

    /**
     * @depends testSetObjectSetsProperty
     */
    public function testGetObjectReturnsPropertyValue()
    {
        $objectData = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ObjectDataInterface');
        $this->objectInFolderData->setObject($objectData);
        $this->assertSame($objectData, $this->objectInFolderData->getObject());
    }

    public function testSetPathSegmentSetsProperty()
    {
        $this->objectInFolderData->setPathSegment('foo');
        $this->assertAttributeSame('foo', 'pathSegment', $this->objectInFolderData);
        $this->objectInFolderData->setPathSegment('bar');
        $this->assertAttributeSame('bar', 'pathSegment', $this->objectInFolderData);
    }

    public function testSetPathSegmentSetsPropertyAsNull()
    {
        $this->objectInFolderData->setPathSegment(null);
        $this->assertAttributeSame(null, 'pathSegment', $this->objectInFolderData);
    }

    /**
     * @depends testSetPathSegmentSetsProperty
     */
    public function testGetPathSegmentReturnsPropertyValue()
    {
        $this->objectInFolderData->setPathSegment('foo');
        $this->assertEquals('foo', $this->objectInFolderData->getPathSegment());
        $this->objectInFolderData->setPathSegment('bar');
        $this->assertEquals('bar', $this->objectInFolderData->getPathSegment());
    }
}
