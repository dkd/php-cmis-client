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

use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\DataObjects\ObjectTypeHelperTrait;
use Dkd\PhpCmis\SessionInterface;
use Dkd\PhpCmis\Test\Unit\ReflectionHelperTrait;
use PHPUnit_Framework_MockObject_MockObject;

class ObjectTypeHelperTraitTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;

    /**
     * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectTypeHelperTrait;

    /**
     * @var SessionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var ObjectTypeInterface
     */
    protected $objectTypeDefinitionMock;

    public function setUp()
    {
        $this->sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getTypeDefinition')
        )->getMockForAbstractClass();
        $this->objectTypeDefinitionMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Data\\ObjectTypeInterface'
        )->getMockForAbstractClass();
        $this->sessionMock->expects($this->any())->method('getTypeDefinition')->willReturn(
            $this->objectTypeDefinitionMock
        );

        $this->objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->getMockForTrait();
        $this->setProtectedProperty($this->objectTypeHelperTrait, 'session', $this->sessionMock);
    }

    public function testGetSessionReturnsSessionProperty()
    {
        $this->assertSame($this->sessionMock, $this->objectTypeHelperTrait->getSession());
    }

    public function testIsBaseTypeReturnsTrueIfGetParentTypeIdIsEmpty()
    {
        /**
         * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject $objectTypeHelperTrait
         */
        $objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->setMethods(
            array('getParentTypeId')
        )->getMockForTrait();
        $objectTypeHelperTrait->expects($this->any())->method('getParentTypeId')->willReturn(null);

        $this->assertTrue($objectTypeHelperTrait->isBaseType());
    }

    public function testIsBaseTypeReturnsFalseIfGetParentTypeIdIsEmpty()
    {
        /**
         * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject $objectTypeHelperTrait
         */
        $objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->setMethods(
            array('getParentTypeId')
        )->getMockForTrait();
        $objectTypeHelperTrait->expects($this->any())->method('getParentTypeId')->willReturn('foo');

        $this->assertFalse($objectTypeHelperTrait->isBaseType());
    }

    /**
     * If the type itself is an base type it can not have an base type.
     */
    public function testGetBaseTypeReturnsNullIfObjectItselfIsAnBaseType()
    {
        /**
         * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject $objectTypeHelperTrait
         */
        $objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->setMethods(
            array('isBaseType')
        )->getMockForTrait();
        $objectTypeHelperTrait->expects($this->any())->method('isBaseType')->willReturn(true);
        $this->assertNull($objectTypeHelperTrait->getBaseType());
    }

    public function testGetBaseTypeReturnsBaseTypePropertyIfPropertyIsNotNull()
    {
        /**
         * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject $objectTypeHelperTrait
         */
        $objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->setMethods(
            array('isBaseType')
        )->getMockForTrait();
        $objectTypeHelperTrait->expects($this->any())->method('isBaseType')->willReturn(false);
        $this->setProtectedProperty($objectTypeHelperTrait, 'baseType', $this->objectTypeDefinitionMock);
        $this->assertSame($this->objectTypeDefinitionMock, $objectTypeHelperTrait->getBaseType());
    }

    public function testGetBaseTypeReturnsNullIfGetBaseTypeIdIsNull()
    {
        /**
         * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject $objectTypeHelperTrait
         */
        $objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->setMethods(
            array('isBaseType', 'getBaseTypeId')
        )->getMockForTrait();
        $objectTypeHelperTrait->expects($this->any())->method('isBaseType')->willReturn(false);
        $objectTypeHelperTrait->expects($this->any())->method('getBaseTypeId')->willReturn(null);
        $this->assertNull($objectTypeHelperTrait->getBaseType());
    }

    public function testGetBaseTypeSetsPropertyAndReturnsBaseTypeDefinition()
    {
        /**
         * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject $objectTypeHelperTrait
         */
        $objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->setMethods(
            array('isBaseType', 'getBaseTypeId', 'getSession')
        )->getMockForTrait();
        $objectTypeHelperTrait->expects($this->any())->method('isBaseType')->willReturn(false);
        $objectTypeHelperTrait->expects($this->any())->method('getBaseTypeId')->willReturn('foo');
        $objectTypeHelperTrait->expects($this->any())->method('getSession')->willReturn($this->sessionMock);
        $this->assertSame($this->objectTypeDefinitionMock, $objectTypeHelperTrait->getBaseType());
        $this->assertAttributeSame($this->objectTypeDefinitionMock, 'baseType', $objectTypeHelperTrait);
    }

    public function testGetParentTypeReturnsParentTypePropertyIfPropertyIsNotNull()
    {
        /**
         * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject $objectTypeHelperTrait
         */
        $objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->setMethods(
            array('functionThatDoesNotExist')
        )->getMockForTrait();
        $this->setProtectedProperty($objectTypeHelperTrait, 'parentType', $this->objectTypeDefinitionMock);
        $this->assertSame($this->objectTypeDefinitionMock, $objectTypeHelperTrait->getParentType());
    }

    public function testGetParentTypeReturnsNullIfGetParentTypeIdIsNull()
    {
        /**
         * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject $objectTypeHelperTrait
         */
        $objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->setMethods(
            array('getParentTypeId')
        )->getMockForTrait();
        $objectTypeHelperTrait->expects($this->any())->method('getParentTypeId')->willReturn(null);
        $this->assertNull($objectTypeHelperTrait->getParentType());
    }

    public function testGetParentTypeSetsPropertyAndReturnsParentTypeDefinition()
    {
        /**
         * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject $objectTypeHelperTrait
         */
        $objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->setMethods(
            array('getParentTypeId', 'getSession')
        )->getMockForTrait();
        $objectTypeHelperTrait->expects($this->any())->method('getParentTypeId')->willReturn('foo');
        $objectTypeHelperTrait->expects($this->any())->method('getSession')->willReturn($this->sessionMock);

        $this->assertSame($this->objectTypeDefinitionMock, $objectTypeHelperTrait->getParentType());
        $this->assertAttributeSame($this->objectTypeDefinitionMock, 'parentType', $objectTypeHelperTrait);
    }

    public function testGetChildrenGetsChildrenFromSession()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getTypeChildren')
        )->getMockForAbstractClass();
        $sessionMock->expects($this->once())->method('getTypeChildren')->with('foo', true);
        /**
         * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject $objectTypeHelperTrait
         */
        $objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->setMethods(array('getId', 'getSession'))->getMockForTrait();
        $objectTypeHelperTrait->expects($this->once())->method('getId')->willReturn('foo');
        $objectTypeHelperTrait->expects($this->once())->method('getSession')->willReturn($sessionMock);

        $objectTypeHelperTrait->getChildren();
    }

    public function testGetDescendantsGetsDescendantsFromSession()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getTypeDescendants')
        )->getMockForAbstractClass();
        $sessionMock->expects($this->once())->method('getTypeDescendants')->with('foo', 1, true);
        /**
         * @var ObjectTypeHelperTrait|PHPUnit_Framework_MockObject_MockObject $objectTypeHelperTrait
         */
        $objectTypeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectTypeHelperTrait'
        )->setMethods(array('getId', 'getSession'))->getMockForTrait();
        $objectTypeHelperTrait->expects($this->once())->method('getId')->willReturn('foo');
        $objectTypeHelperTrait->expects($this->once())->method('getSession')->willReturn($sessionMock);
        $objectTypeHelperTrait->getDescendants(1);
    }
}
