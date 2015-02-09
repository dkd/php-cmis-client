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

use Dkd\PhpCmis\DataObjects\ObjectParentData;

class ObjectParentDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectParentData
     */
    protected $objectParentData;

    public function setUp()
    {
        $this->objectParentData = new ObjectParentData();
    }

    public function testSetObjectSetsProperty()
    {
        $objectData = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ObjectDataInterface');
        $this->objectParentData->setObject($objectData);
        $this->assertAttributeSame($objectData, 'object', $this->objectParentData);
    }

    /**
     * @depends testSetObjectSetsProperty
     */
    public function testGetObjectReturnsPropertyValue()
    {
        $objectData = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ObjectDataInterface');
        $this->objectParentData->setObject($objectData);
        $this->assertSame($objectData, $this->objectParentData->getObject());
    }

    public function testSetRelativePathSegmentSetsProperty()
    {
        $this->objectParentData->setRelativePathSegment('foo');
        $this->assertAttributeSame('foo', 'relativePathSegment', $this->objectParentData);
        $this->objectParentData->setRelativePathSegment('bar');
        $this->assertAttributeSame('bar', 'relativePathSegment', $this->objectParentData);
    }

    /**
     * @depends testSetRelativePathSegmentSetsProperty
     */
    public function testGetRelativePathSegmentReturnsPropertyValue()
    {
        $this->objectParentData->setRelativePathSegment('foo');
        $this->assertEquals('foo', $this->objectParentData->getRelativePathSegment());
        $this->objectParentData->setRelativePathSegment('bar');
        $this->assertEquals('bar', $this->objectParentData->getRelativePathSegment());
    }
}
