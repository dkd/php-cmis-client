<?php
namespace Dkd\PhpCmis\Test\Unit;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\DataObjects\ObjectData;
use Dkd\PhpCmis\ObjectFactory;
use Dkd\PhpCmis\PropertyIds;
use Dkd\PhpCmis\SessionInterface;
use PHPUnit_Framework_MockObject_MockObject;

class ObjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;

    const CLASS_TO_TEST = '\\Dkd\\PhpCmis\\ObjectFactory';

    /**
     * @param SessionInterface|PHPUnit_Framework_MockObject_MockObject $session
     * @return ObjectFactory
     */
    public function getObjectFactory($session = null)
    {
        if ($session === null) {
            $session = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();
        }
        $objectFactory = new ObjectFactory();
        $objectFactory->initialize($session);

        return $objectFactory;
    }

    public function testInitializeSetsGivenSessionAsProperty()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();
        $this->assertAttributeSame($sessionMock, 'session', $this->getObjectFactory($sessionMock));
    }

    public function testGetBindingsObjectFactoryReturnsBindingsObjectFactoryFromSession()
    {
        $bindingMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingInterface')->setMethods(
            array('getObjectFactory')
        )->getMockForAbstractClass();
        $bindingObjectFactoryMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Data\\BindingsObjectFactoryInterface'
        )->getMockForAbstractClass();

        $bindingMock->expects($this->once())->method('getObjectFactory')->willReturn($bindingObjectFactoryMock);
        /** @var SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getBinding')
        )->getMockForAbstractClass();
        $sessionMock->expects($this->once())->method('getBinding')->willReturn($bindingMock);
        $objectFactory = $this->getObjectFactory($sessionMock);

        $method = $this->getMethod(self::CLASS_TO_TEST, 'getBindingsObjectFactory');
        $this->assertSame($bindingObjectFactoryMock, $method->invoke($objectFactory));
    }

    /**
     * @covers Dkd\PhpCmis\ObjectFactory::convertAces
     */
    public function testConvertAcesConvertsAcesToAcl()
    {
        $expectedAcl = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\AclInterface');
        $aces = array(
            $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\AceInterface'),
            $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\AceInterface')
        );

        $bindingMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingInterface')->setMethods(
            array('getObjectFactory')
        )->getMockForAbstractClass();
        $bindingObjectFactoryMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Data\\BindingsObjectFactoryInterface'
        )->setMethods(array('createAccessControlList'))->getMockForAbstractClass();
        $bindingObjectFactoryMock->expects($this->once())->method('createAccessControlList')->with($aces)->willReturn(
            $expectedAcl
        );
        $bindingMock->expects($this->any())->method('getObjectFactory')->willReturn($bindingObjectFactoryMock);
        /** @var SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getBinding')
        )->getMockForAbstractClass();
        $sessionMock->expects($this->any())->method('getBinding')->willReturn($bindingMock);
        $objectFactory = $this->getObjectFactory($sessionMock);

        $this->assertSame($expectedAcl, $objectFactory->convertAces($aces));
    }

    public function testConvertTypeDefinitionThrowsExceptionIfUnknownTypeDefinitionIsGiven()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException', '', 1422028427);
        $this->getObjectFactory()->convertTypeDefinition(
            $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Definitions\\TypeDefinitionInterface')
        );
    }

    /**
     * @dataProvider convertTypeDefinitionDataProvider
     * @param $expectedInstance
     * @param $typeDefinition
     */
    public function testConvertTypeDefinitionConvertsTypeDefinitionToAType($expectedInstance, $typeDefinition)
    {
        $errorReportingLevel = error_reporting(E_ALL & ~E_USER_NOTICE);
        $instance = $this->getObjectFactory()->convertTypeDefinition($typeDefinition);
        error_reporting($errorReportingLevel);
        $this->assertInstanceOf($expectedInstance, $instance);
    }

    /**
     * @return array
     */
    public function convertTypeDefinitionDataProvider()
    {
        return array(
            array(
                '\\Dkd\\PhpCmis\\DataObjects\\DocumentType',
                new \Dkd\PhpCmis\DataObjects\DocumentTypeDefinition('typeId')
            ),
            array(
                '\\Dkd\\PhpCmis\\DataObjects\\FolderType',
                new \Dkd\PhpCmis\DataObjects\FolderTypeDefinition('typeId')
            ),
            array(
                '\\Dkd\\PhpCmis\\DataObjects\\RelationshipType',
                new \Dkd\PhpCmis\DataObjects\RelationshipTypeDefinition('typeId')
            ),
            array(
                '\\Dkd\\PhpCmis\\DataObjects\\PolicyType',
                new \Dkd\PhpCmis\DataObjects\PolicyTypeDefinition('typeId')
            ),
            array(
                '\\Dkd\\PhpCmis\\DataObjects\\ItemType',
                new \Dkd\PhpCmis\DataObjects\ItemTypeDefinition('typeId')
            ),
            array(
                '\\Dkd\\PhpCmis\\DataObjects\\SecondaryType',
                new \Dkd\PhpCmis\DataObjects\SecondaryTypeDefinition('typeId')
            )
        );
    }

    public function testConvertPropertiesReturnsNullIfNoPropertiesGiven()
    {
        $this->assertNull($this->getObjectFactory()->convertProperties(array()));
    }

    public function testConvertPropertiesThrowsExceptionIfSecondaryTypesPropertyIsSetButNotAnArray()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', '', 1425473414);
        $this->getObjectFactory()->convertProperties(
            array(
                PropertyIds::OBJECT_TYPE_ID => 'type-id',
                PropertyIds::SECONDARY_OBJECT_TYPE_IDS => 'invalidValue'
            )
        );
    }

    public function testConvertQueryResult()
    {
        $objectData = new ObjectData();
        $this->assertInstanceOf(
            '\\Dkd\\PhpCmis\\QueryResult',
            $this->getObjectFactory()->convertQueryResult($objectData)
        );
    }

    // TODO Write unit tests for convertProperties
}
