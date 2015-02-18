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

use Dkd\PhpCmis\DataObjects\PolicyType;
use Dkd\PhpCmis\Definitions\PolicyTypeDefinitionInterface;
use PHPUnit_Framework_MockObject_MockObject;

class PolicyTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsSession()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        /**
         * @var PolicyTypeDefinitionInterface|PHPUnit_Framework_MockObject_MockObject $policyTypeDefinition
         */
        $policyTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\PolicyTypeDefinitionInterface'
        )->getMockForAbstractClass();
        $policyTypeDefinition->expects($this->any())->method('getId')->willReturn('typeId');
        $policyTypeDefinition->expects($this->any())->method('getPropertyDefinitions')->willReturn(array());
        $policyType = new PolicyType($sessionMock, $policyTypeDefinition);

        $this->assertAttributeSame($sessionMock, 'session', $policyType);
    }

    public function testConstructorCallsInitializeMethod()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        /**
         * @var PolicyTypeDefinitionInterface|PHPUnit_Framework_MockObject_MockObject $policyTypeDefinition
         */
        $policyTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\PolicyTypeDefinitionInterface'
        )->setMethods(array('getId'))->getMockForAbstractClass();
        $policyTypeDefinition->expects($this->any())->method('getId')->willReturn('typeId');

        /**
         * @var PolicyType|PHPUnit_Framework_MockObject_MockObject $policyType
         */
        $policyType = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\PolicyType')->setMethods(
            array('initialize')
        )->disableOriginalConstructor()->getMock();
        $policyType->expects($this->once())->method('initialize')->with(
            $policyTypeDefinition
        );
        $policyType->__construct($sessionMock, $policyTypeDefinition);
    }
}
