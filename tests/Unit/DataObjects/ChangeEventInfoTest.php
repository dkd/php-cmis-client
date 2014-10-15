<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

use Dkd\PhpCmis\DataObjects\ChangeEventInfo;
use Dkd\PhpCmis\Enum\ChangeType;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class ChangeEventInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChangeEventInfo
     */
    protected $changeEventInfo;

    public function setUp()
    {
        $this->changeEventInfo = new ChangeEventInfo();
    }

    public function testSetChangeTimeSetsProperty()
    {
        $dateTime = new \DateTime();
        $this->changeEventInfo->setChangeTime($dateTime);
        $this->assertAttributeSame($dateTime, 'changeTime', $this->changeEventInfo);
    }

    /**
     * @depends testSetChangeTimeSetsProperty
     */
    public function testGetChangeTimeReturnsProperty()
    {
        $dateTime = new \DateTime();
        $this->changeEventInfo->setChangeTime($dateTime);
        $this->assertSame($dateTime, $this->changeEventInfo->getChangeTime());
    }

    public function testSetChangeTypeSetsProperty()
    {
        $changeType = ChangeType::cast(ChangeType::CREATED);
        $this->changeEventInfo->setchangeType($changeType);
        $this->assertAttributeSame($changeType, 'changeType', $this->changeEventInfo);
    }

    /**
     * @depends testSetChangeTypeSetsProperty
     */
    public function testGetChangeTypeReturnsProperty()
    {
        $changeType = ChangeType::cast(ChangeType::CREATED);
        $this->changeEventInfo->setchangeType($changeType);
        $this->assertSame($changeType, $this->changeEventInfo->getchangeType());
    }
}
