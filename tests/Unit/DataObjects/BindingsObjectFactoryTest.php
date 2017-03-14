<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

/*
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\DataObjects\AccessControlEntry;
use Dkd\PhpCmis\DataObjects\BindingsObjectFactory;
use Dkd\PhpCmis\DataObjects\Principal;
use Dkd\PhpCmis\DataObjects\PropertyBooleanDefinition;
use Dkd\PhpCmis\DataObjects\PropertyDateTimeDefinition;
use Dkd\PhpCmis\DataObjects\PropertyDecimalDefinition;
use Dkd\PhpCmis\DataObjects\PropertyHtmlDefinition;
use Dkd\PhpCmis\DataObjects\PropertyIdDefinition;
use Dkd\PhpCmis\DataObjects\PropertyIntegerDefinition;
use Dkd\PhpCmis\DataObjects\PropertyStringDefinition;
use Dkd\PhpCmis\DataObjects\PropertyUriDefinition;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;

/**
 * Class BindingsObjectFactoryTest
 */
class BindingsObjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BindingsObjectFactory
     */
    protected $bindingsObjectFactory;

    public function setUp()
    {
        $this->bindingsObjectFactory = new BindingsObjectFactory();
    }

    public function testCreateAccessControlEntryReturnsAccessControlEntryObjectWithGivenProperties()
    {
        $principal = 'DummyPrincipal';
        $permissions = ['perm1', 'perm2'];
        $ace = $this->bindingsObjectFactory->createAccessControlEntry($principal, $permissions);

        $this->assertInstanceOf('\\Dkd\\PhpCmis\\DataObjects\\AccessControlEntry', $ace);
        $this->assertEquals(new Principal($principal), $ace->getPrincipal());
        $this->assertSame($permissions, $ace->getPermissions());
    }

    public function testCreateAccessControlListCreatesAnAccessControlListObjectWithGivenAces()
    {
        $aces = [
            $this->getMockBuilder(
                '\\Dkd\\PhpCmis\\Data\\AceInterface'
            )->disableOriginalConstructor()->getMockForAbstractClass(),
            $this->getMockBuilder(
                '\\Dkd\\PhpCmis\\Data\\AceInterface'
            )->disableOriginalConstructor()->getMockForAbstractClass()
        ];
        $acl = $this->bindingsObjectFactory->createAccessControlList($aces);

        $this->assertInstanceOf('\\Dkd\\PhpCmis\\DataObjects\\AccessControlList', $acl);
        $this->assertAttributeSame($aces, 'aces', $acl);
    }

    public function testCreatePropertiesDataCreatesInstanceOfPropertiesObjectWithGivenProperties()
    {
        $property1 = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\AbstractPropertyData')->setConstructorArgs(
            ['property1']
        )->getMockForAbstractClass();
        $property2 = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\AbstractPropertyData')->setConstructorArgs(
            ['property2']
        )->getMockForAbstractClass();
        $properties = $this->bindingsObjectFactory->createPropertiesData([$property1, $property2]);
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\DataObjects\\Properties', $properties);
        $this->assertAttributeSame(
            ['property1' => $property1, 'property2' => $property2],
            'properties',
            $properties
        );
    }

    public function testCreatePropertyDataThrowsExceptionIfUnknownPropertyDefinitionIsGiven()
    {
        /** @var PropertyDefinitionInterface $invalidPropertyDefinition */
        $invalidPropertyDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\PropertyDefinitionInterface'
        )->setMockClassName('InvalidPropertyDefinition')->getMockForAbstractClass();
        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException',
            'Unknown property definition: InvalidPropertyDefinition'
        );
        $this->bindingsObjectFactory->createPropertyData($invalidPropertyDefinition, []);
    }

    public function testCreatePropertyBooleanDataReturnsInstanceOfPropertyBoolean()
    {
        $property = $this->bindingsObjectFactory->createPropertyBooleanData('myId', [true]);
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\DataObjects\\PropertyBoolean', $property);
    }

    public function testCreatePropertyDateTimeDataReturnsInstanceOfPropertyDateTime()
    {
        $property = $this->bindingsObjectFactory->createPropertyDateTimeData('myId', [new \DateTime()]);
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\DataObjects\\PropertyDateTime', $property);
    }

    public function testCreatePropertyDecimalDataReturnsInstanceOfPropertyDecimal()
    {
        $property = $this->bindingsObjectFactory->createPropertyDecimalData('myId', [1.2]);
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\DataObjects\\PropertyDecimal', $property);
    }

    public function testCreatePropertyHtmlDataReturnsInstanceOfPropertyHtml()
    {
        $property = $this->bindingsObjectFactory->createPropertyHtmlData('myId', ['value']);
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\DataObjects\\PropertyHtml', $property);
    }

    public function testCreatePropertyIdDataReturnsInstanceOfPropertyId()
    {
        $property = $this->bindingsObjectFactory->createPropertyIdData('myId', ['value']);
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\DataObjects\\PropertyId', $property);
    }

    public function testCreatePropertyIntegerDataReturnsInstanceOfPropertyInteger()
    {
        $property = $this->bindingsObjectFactory->createPropertyIntegerData('myId', [12]);
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\DataObjects\\PropertyInteger', $property);
    }

    public function testCreatePropertyStringDataReturnsInstanceOfPropertyString()
    {
        $property = $this->bindingsObjectFactory->createPropertyStringData('myId', ['value']);
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\DataObjects\\PropertyString', $property);
    }

    public function testCreatePropertyUriDataReturnsInstanceOfPropertyUri()
    {
        $property = $this->bindingsObjectFactory->createPropertyUriData('myId', ['value']);
        $this->assertInstanceOf('\\Dkd\\PhpCmis\\DataObjects\\PropertyUri', $property);
    }

    /**
     * @param PropertyDefinitionInterface $propertyDefinition
     * @param array $values
     * @param string $expectedPropertyClass
     * @dataProvider createPropertyDataDataProvider
     */
    public function testCreatePropertyDataCreatesPropertyBasedOnTheGivenPropertyDefinition(
        PropertyDefinitionInterface $propertyDefinition,
        array $values,
        $expectedPropertyClass
    ) {
        $this->assertInstanceOf(
            $expectedPropertyClass,
            $this->bindingsObjectFactory->createPropertyData($propertyDefinition, $values)
        );
    }

    public function createPropertyDataDataProvider()
    {
        return [
            [
                new PropertyBooleanDefinition('testId'),
                [true],
                '\\Dkd\\PhpCmis\\DataObjects\\PropertyBoolean'
            ],
            [
                new PropertyDateTimeDefinition('testId'),
                [new \DateTime()],
                '\\Dkd\\PhpCmis\\DataObjects\\PropertyDateTime'
            ],
            [
                new PropertyDecimalDefinition('testId'),
                [1.2],
                '\\Dkd\\PhpCmis\\DataObjects\\PropertyDecimal'
            ],
            [
                new PropertyHtmlDefinition('testId'),
                ['testValue'],
                '\\Dkd\\PhpCmis\\DataObjects\\PropertyHtml'
            ],
            [
                new PropertyIdDefinition('testId'),
                ['testValue'],
                '\\Dkd\\PhpCmis\\DataObjects\\PropertyId'
            ],
            [
                new PropertyIntegerDefinition('testId'),
                [12],
                '\\Dkd\\PhpCmis\\DataObjects\\PropertyInteger'
            ],
            [
                new PropertyStringDefinition('testId'),
                ['testValue'],
                '\\Dkd\\PhpCmis\\DataObjects\\PropertyString'
            ],
            [
                new PropertyUriDefinition('testId'),
                ['testValue'],
                '\\Dkd\\PhpCmis\\DataObjects\\PropertyUri'
            ],
        ];
    }
}
