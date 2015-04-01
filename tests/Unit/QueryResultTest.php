<?php
namespace Dkd\PhpCmis\Test\Unit;

use Dkd\PhpCmis\DataObjects\AllowableActions;
use Dkd\PhpCmis\DataObjects\ObjectData;
use Dkd\PhpCmis\DataObjects\Properties;
use Dkd\PhpCmis\DataObjects\PropertyInteger;
use Dkd\PhpCmis\DataObjects\PropertyString;
use Dkd\PhpCmis\ObjectFactory;
use Dkd\PhpCmis\OperationContext;
use Dkd\PhpCmis\QueryResult;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class QueryResultTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;

    public function testConstructorSetsPropertiesByIdFromGivenObjectData()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        $objectFactory = new ObjectFactory();
        $objectFactory->initialize($sessionMock);
        $operationContext = new OperationContext();

        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactory);
        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);

        $property1 = new PropertyString('foo', 'foobar');
        $property1->setQueryName('baz:foo');

        $property2 = new PropertyInteger('bar', 123);
        $property2->setQueryName('baz:bar');

        $properties = new Properties();
        $properties->addProperties(array($property1, $property2));

        $objectData = new ObjectData();
        $objectData->setProperties($properties);

        $queryResult = new QueryResult($sessionMock, $objectData);

        $expectedPropertiesById = array(
            'foo' => $property1,
            'bar' => $property2
        );

        $this->assertAttributeEquals($expectedPropertiesById, 'propertiesById', $queryResult);
    }

    public function testConstructorSetsPropertiesByQueryNameFromGivenObjectData()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        $objectFactory = new ObjectFactory();
        $objectFactory->initialize($sessionMock);
        $operationContext = new OperationContext();

        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactory);
        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);

        $property1 = new PropertyString('foo', 'foobar');
        $property1->setQueryName('baz:foo');

        $property2 = new PropertyInteger('bar', 123);
        $property2->setQueryName('baz:bar');

        $properties = new Properties();
        $properties->addProperties(array($property1, $property2));

        $objectData = new ObjectData();
        $objectData->setProperties($properties);

        $queryResult = new QueryResult($sessionMock, $objectData);

        $expectedPropertiesByQueryName = array(
            'baz:foo' => $property1,
            'baz:bar' => $property2
        );

        $this->assertAttributeEquals($expectedPropertiesByQueryName, 'propertiesByQueryName', $queryResult);
    }

    public function testConstructorSetsRelationshipsFromGivenObjectData()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        /** @var ObjectFactory|PHPUnit_Framework_MockObject_MockObject $objectFactoryMock */
        $objectFactoryMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectFactory')->setMethods(
            array('convertObject')
        )->getMock();
        $objectFactoryMock->initialize($sessionMock);
        $operationContext = new OperationContext();

        $relationshipObjectData1 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectData'
        )->disableOriginalConstructor()->getMock();

        $relationshipObjectData2 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectData'
        )->disableOriginalConstructor()->getMock();

        $expectedRelationship1 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\Relationship'
        )->disableOriginalConstructor()->getMock();

        $expectedRelationship2 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\Relationship'
        )->disableOriginalConstructor()->getMock();

        $expectedRelationships = array($expectedRelationship1, $expectedRelationship2);

        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);
        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactoryMock);

        $objectFactoryMock->expects($this->any())->method('convertObject')->will(
            $this->returnValueMap(
                array(
                    array($relationshipObjectData1, $operationContext, $expectedRelationship1),
                    array($relationshipObjectData2, $operationContext, $expectedRelationship2)
                )
            )
        );

        $objectData = new ObjectData();
        $objectData->setRelationships(array($relationshipObjectData1, $relationshipObjectData2));

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertAttributeEquals($expectedRelationships, 'relationships', $queryResult);
    }

    public function testConstructorSetsRenditionsFromGivenObjectData()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        /** @var ObjectFactory|PHPUnit_Framework_MockObject_MockObject $objectFactoryMock */
        $objectFactoryMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectFactory')->setMethods(
            array('convertRendition')
        )->getMock();
        $objectFactoryMock->initialize($sessionMock);
        $operationContext = new OperationContext();

        $renditionData1 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\RenditionData'
        )->disableOriginalConstructor()->getMock();

        $renditionData2 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\RenditionData'
        )->disableOriginalConstructor()->getMock();

        $expectedRendition1 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\Rendition'
        )->disableOriginalConstructor()->getMock();

        $expectedRendition2 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\Rendition'
        )->disableOriginalConstructor()->getMock();

        $expectedRenditions = array($expectedRendition1, $expectedRendition2);

        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);
        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactoryMock);

        $objectFactoryMock->expects($this->any())->method('convertRendition')->will(
            $this->returnValueMap(
                array(
                    array(null, $renditionData1, $expectedRendition1),
                    array(null, $renditionData2, $expectedRendition2)
                )
            )
        );

        $objectData = new ObjectData();
        $objectData->setRenditions(array($renditionData1, $renditionData2));

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertAttributeEquals($expectedRenditions, 'renditions', $queryResult);
    }

    public function testConstructorSetsAllowableActionsFromGivenObjectData()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        $objectFactory = new ObjectFactory();
        $operationContext = new OperationContext();

        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);
        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactory);

        $allowableActions = new AllowableActions();
        $objectData = new ObjectData();
        $objectData->setAllowableActions($allowableActions);

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertAttributeEquals($allowableActions, 'allowableActions', $queryResult);
    }

    public function testGetAllowableActionsReturnsAllowableActions()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        $objectFactory = new ObjectFactory();
        $operationContext = new OperationContext();

        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);
        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactory);

        $allowableActions = new AllowableActions();
        $objectData = new ObjectData();
        $objectData->setAllowableActions($allowableActions);

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertEquals($allowableActions, $queryResult->getAllowableActions());
    }

    public function testGetPropertiesReturnsProperties()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        $objectFactory = new ObjectFactory();
        $objectFactory->initialize($sessionMock);
        $operationContext = new OperationContext();

        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactory);
        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);

        $property1 = new PropertyString('foo', 'foobar');
        $property1->setQueryName('baz:foo');

        $property2 = new PropertyInteger('bar', 123);
        $property2->setQueryName('baz:bar');

        $properties = new Properties();
        $properties->addProperties(array($property1, $property2));

        $objectData = new ObjectData();
        $objectData->setProperties($properties);

        $queryResult = new QueryResult($sessionMock, $objectData);

        $expectedProperties = array($property1, $property2);

        $this->assertEquals($expectedProperties, $queryResult->getProperties());
    }

    public function testGetPropertyByIdReturnsSelectedProperty()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        $objectFactory = new ObjectFactory();
        $objectFactory->initialize($sessionMock);
        $operationContext = new OperationContext();

        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactory);
        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);

        $property1 = new PropertyString('foo', 'foobar');
        $property1->setQueryName('baz:foo');

        $property2 = new PropertyInteger('bar', 123);
        $property2->setQueryName('baz:bar');

        $properties = new Properties();
        $properties->addProperties(array($property1, $property2));

        $objectData = new ObjectData();
        $objectData->setProperties($properties);

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertEquals($property1, $queryResult->getPropertyById('foo'));
        $this->assertEquals($property2, $queryResult->getPropertyById('bar'));
        $this->assertEquals(null, $queryResult->getPropertyById('notExists'));
    }

    public function testGetPropertyByQueryNameReturnsSelectedProperty()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        $objectFactory = new ObjectFactory();
        $objectFactory->initialize($sessionMock);
        $operationContext = new OperationContext();

        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactory);
        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);

        $property1 = new PropertyString('foo', 'foobar');
        $property1->setQueryName('baz:foo');

        $property2 = new PropertyInteger('bar', 123);
        $property2->setQueryName('baz:bar');

        $properties = new Properties();
        $properties->addProperties(array($property1, $property2));

        $objectData = new ObjectData();
        $objectData->setProperties($properties);

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertEquals($property1, $queryResult->getPropertyByQueryName('baz:foo'));
        $this->assertEquals($property2, $queryResult->getPropertyByQueryName('baz:bar'));
        $this->assertEquals(null, $queryResult->getPropertyByQueryName('not:exists'));
    }

    public function testGetPropertyMultivalueByIdReturnsPropertyValuesFromSelectedProperty()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        $objectFactory = new ObjectFactory();
        $objectFactory->initialize($sessionMock);
        $operationContext = new OperationContext();

        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactory);
        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);

        $values1 = array('foobar', 'barbaz', 'foobaz');
        $property1 = new PropertyString('foo', $values1);
        $property1->setQueryName('baz:foo');

        $values2 = array(123, 234, 345);
        $property2 = new PropertyInteger('bar', $values2);
        $property2->setQueryName('baz:bar');

        $properties = new Properties();
        $properties->addProperties(array($property1, $property2));

        $objectData = new ObjectData();
        $objectData->setProperties($properties);

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertEquals($values1, $queryResult->getPropertyMultivalueById('foo'));
        $this->assertEquals($values2, $queryResult->getPropertyMultivalueById('bar'));
        $this->assertEquals(null, $queryResult->getPropertyMultivalueById('not:exists'));
    }

    public function testGetPropertyMultivalueByQueryNameReturnsPropertyValuesFromSelectedProperty()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        $objectFactory = new ObjectFactory();
        $objectFactory->initialize($sessionMock);
        $operationContext = new OperationContext();

        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactory);
        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);

        $values1 = array('foobar', 'barbaz', 'foobaz');
        $property1 = new PropertyString('foo', $values1);
        $property1->setQueryName('baz:foo');

        $values2 = array(123, 234, 345);
        $property2 = new PropertyInteger('bar', $values2);
        $property2->setQueryName('baz:bar');

        $properties = new Properties();
        $properties->addProperties(array($property1, $property2));

        $objectData = new ObjectData();
        $objectData->setProperties($properties);

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertEquals($values1, $queryResult->getPropertyMultivalueByQueryName('baz:foo'));
        $this->assertEquals($values2, $queryResult->getPropertyMultivalueByQueryName('baz:bar'));
        $this->assertEquals(null, $queryResult->getPropertyMultivalueByQueryName('not:exists'));
    }

    public function testGetPropertyValueByIdReturnsFirstPropertyValueFromSelectedProperty()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        $objectFactory = new ObjectFactory();
        $objectFactory->initialize($sessionMock);
        $operationContext = new OperationContext();

        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactory);
        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);

        $values1 = array('foobar', 'barbaz', 'foobaz');
        $property1 = new PropertyString('foo', $values1);
        $property1->setQueryName('baz:foo');

        $values2 = array(123, 234, 345);
        $property2 = new PropertyInteger('bar', $values2);
        $property2->setQueryName('baz:bar');

        $properties = new Properties();
        $properties->addProperties(array($property1, $property2));

        $objectData = new ObjectData();
        $objectData->setProperties($properties);

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertEquals('foobar', $queryResult->getPropertyValueById('foo'));
        $this->assertEquals(123, $queryResult->getPropertyValueById('bar'));
        $this->assertEquals(null, $queryResult->getPropertyValueById('not:exists'));
    }

    public function testGetPropertyValueByQueryNameReturnsFirstPropertyValueFromSelectedProperty()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        $objectFactory = new ObjectFactory();
        $objectFactory->initialize($sessionMock);
        $operationContext = new OperationContext();

        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactory);
        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);

        $values1 = array('foobar', 'barbaz', 'foobaz');
        $property1 = new PropertyString('foo', $values1);
        $property1->setQueryName('baz:foo');

        $values2 = array(123, 234, 345);
        $property2 = new PropertyInteger('bar', $values2);
        $property2->setQueryName('baz:bar');

        $properties = new Properties();
        $properties->addProperties(array($property1, $property2));

        $objectData = new ObjectData();
        $objectData->setProperties($properties);

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertEquals('foobar', $queryResult->getPropertyValueByQueryName('baz:foo'));
        $this->assertEquals(123, $queryResult->getPropertyValueByQueryName('baz:bar'));
        $this->assertEquals(null, $queryResult->getPropertyValueByQueryName('not:exists'));
    }

    public function testGetRelationshipsReturnsRelationships()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        /** @var ObjectFactory|PHPUnit_Framework_MockObject_MockObject $objectFactoryMock */
        $objectFactoryMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectFactory')->setMethods(
            array('convertObject')
        )->getMock();
        $objectFactoryMock->initialize($sessionMock);
        $operationContext = new OperationContext();

        $relationshipObjectData1 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectData'
        )->disableOriginalConstructor()->getMock();

        $relationshipObjectData2 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\ObjectData'
        )->disableOriginalConstructor()->getMock();

        $expectedRelationship1 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\Relationship'
        )->disableOriginalConstructor()->getMock();

        $expectedRelationship2 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\Relationship'
        )->disableOriginalConstructor()->getMock();

        $expectedRelationships = array($expectedRelationship1, $expectedRelationship2);

        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);
        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactoryMock);

        $objectFactoryMock->expects($this->any())->method('convertObject')->will(
            $this->returnValueMap(
                array(
                    array($relationshipObjectData1, $operationContext, $expectedRelationship1),
                    array($relationshipObjectData2, $operationContext, $expectedRelationship2)
                )
            )
        );

        $objectData = new ObjectData();
        $objectData->setRelationships(array($relationshipObjectData1, $relationshipObjectData2));

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertEquals($expectedRelationships, $queryResult->getRelationships());
    }

    public function testGetRenditionsReturnsRenditions()
    {
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->setMethods(
            array('getObjectFactory', 'getDefaultContext')
        )->getMockForAbstractClass();

        /** @var ObjectFactory|PHPUnit_Framework_MockObject_MockObject $objectFactoryMock */
        $objectFactoryMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectFactory')->setMethods(
            array('convertRendition')
        )->getMock();
        $objectFactoryMock->initialize($sessionMock);
        $operationContext = new OperationContext();

        $renditionData1 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\RenditionData'
        )->disableOriginalConstructor()->getMock();

        $renditionData2 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\RenditionData'
        )->disableOriginalConstructor()->getMock();

        $expectedRendition1 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\Rendition'
        )->disableOriginalConstructor()->getMock();

        $expectedRendition2 = $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\Rendition'
        )->disableOriginalConstructor()->getMock();

        $expectedRenditions = array($expectedRendition1, $expectedRendition2);

        $sessionMock->expects($this->any())->method('getDefaultContext')->willReturn($operationContext);
        $sessionMock->expects($this->once())->method('getObjectFactory')->willReturn($objectFactoryMock);

        $objectFactoryMock->expects($this->any())->method('convertRendition')->will(
            $this->returnValueMap(
                array(
                    array(null, $renditionData1, $expectedRendition1),
                    array(null, $renditionData2, $expectedRendition2)
                )
            )
        );

        $objectData = new ObjectData();
        $objectData->setRenditions(array($renditionData1, $renditionData2));

        $queryResult = new QueryResult($sessionMock, $objectData);

        $this->assertEquals($expectedRenditions, $queryResult->getRenditions());
    }
}
