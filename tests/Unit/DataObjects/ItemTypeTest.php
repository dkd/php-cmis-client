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

use Dkd\PhpCmis\Definitions\ItemTypeDefinitionInterface;
use PHPUnit_Framework_MockObject_MockObject;

class ItemTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsSession()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        /**
         * @var ItemTypeDefinitionInterface|PHPUnit_Framework_MockObject_MockObject $itemTypeDefinition
         */
        $itemTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\MutableItemTypeDefinitionInterface'
        )->getMockForAbstractClass();

        $itemType = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\ItemType')->setConstructorArgs(
            array($sessionMock, $itemTypeDefinition)
        )->getMock();

        $this->assertAttributeSame($sessionMock, 'session', $itemType);
    }

    public function testConstructorCallsInitializeMethod()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        /**
         * @var ItemTypeDefinitionInterface|PHPUnit_Framework_MockObject_MockObject $itemTypeDefinition
         */
        $itemTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\MutableItemTypeDefinitionInterface'
        )->getMockForAbstractClass();
        $itemType = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\ItemType')->setMethods(
            array('initialize')
        )->disableOriginalConstructor()->getMock();
        $itemType->expects($this->once())->method('initialize')->with(
            $itemTypeDefinition
        );
        $itemType->__construct($sessionMock, $itemTypeDefinition);
    }
}
