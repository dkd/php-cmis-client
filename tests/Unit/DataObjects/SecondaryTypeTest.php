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

use Dkd\PhpCmis\Definitions\SecondaryTypeDefinitionInterface;
use PHPUnit_Framework_MockObject_MockObject;

class SecondaryTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsSession()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        /**
         * @var SecondaryTypeDefinitionInterface|PHPUnit_Framework_MockObject_MockObject $secondaryTypeDefinition
         */
        $secondaryTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\MutableSecondaryTypeDefinitionInterface'
        )->getMockForAbstractClass();

        $secondaryType = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\SecondaryType')->setConstructorArgs(
            array($sessionMock, $secondaryTypeDefinition)
        )->getMock();

        $this->assertAttributeSame($sessionMock, 'session', $secondaryType);
    }

    public function testConstructorCallsInitializeMethod()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        /**
         * @var SecondaryTypeDefinitionInterface|PHPUnit_Framework_MockObject_MockObject $secondaryTypeDefinition
         */
        $secondaryTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\MutableSecondaryTypeDefinitionInterface'
        )->getMockForAbstractClass();
        $secondaryType = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\SecondaryType')->setMethods(
            array('initialize')
        )->disableOriginalConstructor()->getMock();
        $secondaryType->expects($this->once())->method('initialize')->with(
            $secondaryTypeDefinition
        );
        $secondaryType->__construct($sessionMock, $secondaryTypeDefinition);
    }
}
