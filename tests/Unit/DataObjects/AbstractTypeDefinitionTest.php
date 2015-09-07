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

use Dkd\PhpCmis\DataObjects\AbstractTypeDefinition;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class AbstractTypeDefinitionTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var AbstractTypeDefinition
     */
    protected $abstractTypeDefinition;

    protected $stringProperties = array(
        'localName',
        'localNamespace',
        'queryName',
        'displayName',
        'description',
        'parentTypeId'
    );

    protected $booleanProperties = array(
        'isCreatable',
        'isFileable',
        'isQueryable',
        'isIncludedInSupertypeQuery',
        'isFulltextIndexed',
        'isControllableAcl',
        'isControllablePolicy'
    );

    protected $objectProperties = array(
        'baseTypeId',
        'propertyDefinitions',
        'typeMutability'
    );

    public function setUp()
    {
        $this->abstractTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\AbstractTypeDefinition'
        )->setConstructorArgs(array('typeId'))->getMockForAbstractClass();
    }

    public function stringPropertyDataProvider()
    {
        $stringPropertyData = array();
        foreach ($this->stringProperties as $propertyName) {
            foreach ($this->stringCastDataProvider() as $stringPropertyTestValues) {
                $testDataSet = $stringPropertyTestValues;
                // allow/expect null value for property parentTypeId
                if ($testDataSet[1] === null && $propertyName === 'parentTypeId') {
                    $testDataSet[0] = null;
                }
                array_unshift($testDataSet, $propertyName);
                $stringPropertyData[] = $testDataSet;
            }
        }

        return $stringPropertyData;
    }

    public function booleanPropertyDataProvider()
    {
        $booleanPropertyData = array();
        foreach ($this->booleanProperties as $propertyName) {
            foreach ($this->booleanCastDataProvider() as $booleanPropertyTestValues) {
                $testDataSet = $booleanPropertyTestValues;
                // Do not expect null values to be casted because they are allowed.
                // So the expected result for a given "null" is also "null" and not an empty string.
                if ($testDataSet[1] === null) {
                    $testDataSet[0] = null;
                }
                array_unshift($testDataSet, $propertyName);
                $booleanPropertyData[] = $testDataSet;
            }
        }

        return $booleanPropertyData;
    }

    public function objectPropertyDataProvider()
    {
        $baseTypeId = BaseTypeId::cast(BaseTypeId::CMIS_ITEM);
        $propertyDefinition = $this->getMockBuilder('\\Dkd\\PhpCmis\\Definitions\\PropertyDefinitionInterface')
                                   ->setMethods(array('getId'))->getMockForAbstractClass();
        $propertyDefinition->expects($this->any())->method('getId')->willReturn('fooId');
        $propertyDefinitions = array('fooId' => $propertyDefinition);
        $typeMutability = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\TypeMutabilityInterface'
        )->getMockForAbstractClass();

        return array(
            array('baseTypeId', $baseTypeId, $baseTypeId),
            array('propertyDefinitions', $propertyDefinitions, $propertyDefinitions),
            array('typeMutability', $typeMutability, $typeMutability),
        );
    }

    public function propertyDataProvider()
    {
        return array_merge(
            $this->stringPropertyDataProvider(),
            $this->booleanPropertyDataProvider(),
            $this->objectPropertyDataProvider()
        );
    }

    /**
     * Test setter for a property - should cast given value to expected type
     *
     * @dataProvider propertyDataProvider
     * @param string $propertyName
     * @param mixed $propertyValue
     * @param mixed $expectedAttributeValue
     */
    public function testPropertySetterSetsPropertyAndCastsToExpectedType(
        $propertyName,
        $expectedAttributeValue,
        $propertyValue
    ) {
        $setterName = 'set' . ucfirst($propertyName);
        @$this->abstractTypeDefinition->$setterName($propertyValue);
        $this->assertAttributeSame(
            $expectedAttributeValue,
            $propertyName,
            $this->abstractTypeDefinition,
            sprintf(
                'Calling %s with "%s" has not set the property to the expected value "%s"',
                $setterName,
                is_scalar($propertyValue) ? $propertyValue : getType($propertyValue),
                is_scalar($expectedAttributeValue) ? $expectedAttributeValue : getType($expectedAttributeValue)
            )
        );
    }

    /**
     * Test getter for a property
     *
     * @depends      testPropertySetterSetsPropertyAndCastsToExpectedType
     * @dataProvider propertyDataProvider
     * @param string $propertyName
     * @param mixed $expectedAttributeValue
     * @param mixed $propertyValue
     */
    public function testPropertyGetterReturnsPropertyValue($propertyName, $expectedAttributeValue, $propertyValue)
    {
        $setterName = 'set' . ucfirst($propertyName);
        if (preg_match('/^is[A-z].*/', $propertyName)) {
            $getterName = $propertyName;
        } else {
            $getterName = 'get' . ucfirst($propertyName);
        }
        @$this->abstractTypeDefinition->$setterName($propertyValue);
        $this->assertSame(
            $expectedAttributeValue,
            $this->abstractTypeDefinition->$getterName()
        );
    }

    public function testAddPropertyDefinitionAddsPropertyDefinitionWithPropertyDefinitionIdAsArrayIndex()
    {
        /** @var PropertyDefinitionInterface|\PHPUnit_Framework_MockObject_MockObject $propertyDefinition */
        $propertyDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\PropertyDefinitionInterface'
        )->setMethods(array('getId'))->getMockForAbstractClass();
        $propertyDefinition->expects($this->any())->method('getId')->willReturn('fooId');

        $this->abstractTypeDefinition->addPropertyDefinition($propertyDefinition);
        $this->assertAttributeSame(
            array('fooId' => $propertyDefinition),
            'propertyDefinitions',
            $this->abstractTypeDefinition
        );

        return $this->abstractTypeDefinition;
    }

    public function testGetPropertyDefinitionReturnsNullIfNoDefinitionWithGivenIdExists()
    {
        $this->assertNull($this->abstractTypeDefinition->getPropertyDefinition('invalidId'));
    }

    /**
     * @depends testAddPropertyDefinitionAddsPropertyDefinitionWithPropertyDefinitionIdAsArrayIndex
     * @param AbstractTypeDefinition $abstractTypeDefinition
     */
    public function testGetPropertyDefinitionReturnsPropertyDefinitionForGivenId($abstractTypeDefinition)
    {
        $expected = $this->getMockBuilder('\\Dkd\\PhpCmis\\Definitions\\PropertyDefinitionInterface')->setMethods(
            array('getId')
        )->getMockForAbstractClass();
        $this->assertEquals($expected, $abstractTypeDefinition->getPropertyDefinition('fooId'));
    }

    public function testPopulateWithClonesMethodCopiesPropertyValuesFromGivenTypeDefinitionButIgnoresNullValues()
    {
        $dummyTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\AbstractTypeDefinition'
        )->setConstructorArgs(array('typeId'))->getMockForAbstractClass();

        $errorReportingLevel = error_reporting(E_ALL & ~E_USER_NOTICE);
        $this->abstractTypeDefinition->populateWithClones($dummyTypeDefinition);
        error_reporting($errorReportingLevel);
        foreach (array_merge($this->stringProperties, $this->booleanProperties, $this->objectProperties) as $property) {
            if ($property !== 'propertyDefinitions') {
                $this->assertAttributeEquals(null, $property, $this->abstractTypeDefinition);
            } else {
                $this->assertAttributeEmpty($property, $this->abstractTypeDefinition);
            }
        }
    }

    public function testPopulateWithClonesMethodCopiesPropertyValuesFromGivenTypeDefinition()
    {
        /** @var AbstractTypeDefinition|\PHPUnit_Framework_MockObject_MockObject $dummyTypeDefinition */
        $dummyTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\AbstractTypeDefinition'
        )->setConstructorArgs(array('typeId'))->getMockForAbstractClass();

        foreach ($this->stringProperties as $stringProperty) {
            $setterName = 'set' . ucfirst($stringProperty);
            $dummyTypeDefinition->$setterName('dummyStringValue');
        }
        foreach ($this->booleanProperties as $booleanProperty) {
            $setterName = 'set' . ucfirst($booleanProperty);
            $dummyTypeDefinition->$setterName(true);
        }
        foreach ($this->objectPropertyDataProvider() as $objectProperty) {
            $setterName = 'set' . ucfirst($objectProperty[0]);
            $dummyTypeDefinition->$setterName($objectProperty[1]);
        }

        $this->abstractTypeDefinition->populateWithClones($dummyTypeDefinition);

        $this->assertEquals($dummyTypeDefinition, $this->abstractTypeDefinition);
    }
}
