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

use Dkd\PhpCmis\DataObjects\ItemType;
use Dkd\PhpCmis\DataObjects\ItemTypeDefinition;
use PHPUnit_Framework_MockObject_MockObject;

class ItemTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsSession()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        $itemTypeDefinition = new ItemTypeDefinition('typeId');
        $errorReportingLevel = error_reporting(E_ALL & ~E_USER_NOTICE);
        $itemType = new ItemType($sessionMock, $itemTypeDefinition);
        error_reporting($errorReportingLevel);

        $this->assertAttributeSame($sessionMock, 'session', $itemType);
    }

    public function testConstructorCallsPopulateMethod()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        $itemTypeDefinition = new ItemTypeDefinition('typeId');

        /**
         * @var ItemType|PHPUnit_Framework_MockObject_MockObject $itemType
         */
        $itemType = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\ItemType')->setMethods(
            array('populate')
        )->disableOriginalConstructor()->getMock();
        $itemType->expects($this->once())->method('populate')->with(
            $itemTypeDefinition
        );
        $itemType->__construct($sessionMock, $itemTypeDefinition);
    }
}
