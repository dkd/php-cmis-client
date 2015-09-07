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
use Dkd\PhpCmis\DataObjects\RelationshipType;
use Dkd\PhpCmis\DataObjects\RelationshipTypeDefinition;
use Dkd\PhpCmis\SessionInterface;
use Dkd\PhpCmis\Test\Unit\ReflectionHelperTrait;
use PHPUnit_Framework_MockObject_MockObject;

class RelationshipTypeTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;

    /**
     * @var SessionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var RelationshipType
     */
    protected $relationshipType;

    /**
     * @var ObjectTypeInterface
     */
    protected $objectTypeDefinitionMock;

    /**
     * @covers \Dkd\PhpCmis\DataObjects\RelationshipType::__construct
     */
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

        $errorReportingLevel = error_reporting(E_ALL & ~E_USER_NOTICE);
        $this->relationshipType = new RelationshipType($this->sessionMock, new RelationshipTypeDefinition('typeId'));
        error_reporting($errorReportingLevel);
    }

    public function testConstructorSetsSession()
    {
        $this->assertAttributeSame($this->sessionMock, 'session', $this->relationshipType);
    }

    /**
     * @covers \Dkd\PhpCmis\DataObjects\RelationshipType::__construct
     */
    public function testConstructorCallsPopulateMethod()
    {
        $relationshipTypeDefinition = new RelationshipTypeDefinition('typeId');

        /**
         * @var RelationshipType|PHPUnit_Framework_MockObject_MockObject $relationshipType
         */
        $relationshipType = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\RelationshipType')->setMethods(
            array('populate')
        )->disableOriginalConstructor()->getMock();

        $relationshipType->expects($this->once())->method('populate')->with(
            $relationshipTypeDefinition
        );
        $relationshipType->__construct($this->sessionMock, $relationshipTypeDefinition);
    }

    public function testSetAllowedSourceTypeIdsResetsAllowedSourceTypesParameter()
    {
        // set the property to a dummy value
        $this->setProtectedProperty($this->relationshipType, 'allowedSourceTypes', array('foo', 'bar'));

        $this->relationshipType->setAllowedSourceTypeIds(array('baz'));
        $this->assertAttributeEquals(null, 'allowedSourceTypes', $this->relationshipType);
    }

    public function testSetAllowedTargetTypeIdsResetsAllowedTargetTypesParameter()
    {
        // set the property to a dummy value
        $this->setProtectedProperty($this->relationshipType, 'allowedTargetTypes', array('foo', 'bar'));

        $this->relationshipType->setAllowedTargetTypeIds(array('baz'));
        $this->assertAttributeEquals(null, 'allowedTargetTypes', $this->relationshipType);
    }

    public function testGetAllowedSourceTypesReturnsPropertyValue()
    {
        $allowedSourceTypes =  array('foo', 'bar');
        // set the property to a dummy value
        $this->setProtectedProperty($this->relationshipType, 'allowedSourceTypes', $allowedSourceTypes);
        $this->assertSame($allowedSourceTypes, $this->relationshipType->getAllowedSourceTypes());
    }

    public function testGetAllowedSourceTypesSetsAllowedSourceTypesPropertyGeneratedOnIdsAndReturnsResult()
    {
        $this->setProtectedProperty($this->relationshipType, 'allowedSourceTypeIds', array('foo'));
        $this->assertSame(array($this->objectTypeDefinitionMock), $this->relationshipType->getAllowedSourceTypes());
    }

    public function testGetAllowedTargetTypesReturnsPropertyValue()
    {
        $allowedTargetTypes =  array('foo', 'bar');
        // set the property to a dummy value
        $this->setProtectedProperty($this->relationshipType, 'allowedTargetTypes', $allowedTargetTypes);
        $this->assertSame($allowedTargetTypes, $this->relationshipType->getAllowedTargetTypes());
    }

    public function testGetAllowedTargetTypesSetsAllowedTargetTypesPropertyGeneratedOnIdsAndReturnsResult()
    {
        $this->setProtectedProperty($this->relationshipType, 'allowedTargetTypeIds', array('foo'));
        $this->assertSame(array($this->objectTypeDefinitionMock), $this->relationshipType->getAllowedTargetTypes());
    }
}
