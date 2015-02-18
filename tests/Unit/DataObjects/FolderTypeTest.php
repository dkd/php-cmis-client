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

use Dkd\PhpCmis\DataObjects\FolderType;
use Dkd\PhpCmis\Definitions\FolderTypeDefinitionInterface;
use PHPUnit_Framework_MockObject_MockObject;

class FolderTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsSession()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        /**
         * @var FolderTypeDefinitionInterface|PHPUnit_Framework_MockObject_MockObject $folderTypeDefinition
         */
        $folderTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\MutableFolderTypeDefinitionInterface'
        )->setMethods(array('getId'))->getMockForAbstractClass();
        $folderTypeDefinition->expects($this->any())->method('getId')->willReturn('typeId');
        $folderTypeDefinition->expects($this->any())->method('getPropertyDefinitions')->willReturn(array());
        $folderType = new FolderType($sessionMock, $folderTypeDefinition);

        $this->assertAttributeSame($sessionMock, 'session', $folderType);
    }

    public function testConstructorCallsInitializeMethod()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        /**
         * @var FolderTypeDefinitionInterface|PHPUnit_Framework_MockObject_MockObject $folderTypeDefinition
         */
        $folderTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\MutableFolderTypeDefinitionInterface'
        )->setMethods(array('getId'))->getMockForAbstractClass();
        $folderTypeDefinition->expects($this->any())->method('getId')->willReturn('typeId');

        /**
         * @var FolderType|PHPUnit_Framework_MockObject_MockObject $folderType
         */
        $folderType = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\FolderType')->setMethods(
            array('initialize')
        )->disableOriginalConstructor()->getMock();
        $folderType->expects($this->once())->method('initialize')->with(
            $folderTypeDefinition
        );
        $folderType->__construct($sessionMock, $folderTypeDefinition);
    }
}
