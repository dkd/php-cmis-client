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

use Dkd\PhpCmis\DataObjects\PropertyIntegerDefinition;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyIntegerDefinitionTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyIntegerDefinition
     */
    protected $propertyIntegerDefinition;

    public function setUp()
    {
        $this->propertyIntegerDefinition = new PropertyIntegerDefinition('testId');
    }

    public function testAssertIsInstanceOfAbstractPropertyDefinition()
    {
        $this->assertInstanceOf(
            '\\Dkd\\PhpCmis\\DataObjects\\AbstractPropertyDefinition',
            $this->propertyIntegerDefinition
        );
    }

    /**
     * @dataProvider integerCastDataProvider
     * @param integer $expected
     * @param mixed $value
     */
    public function testSetMaxValueCastsValueToIntegerAndSetsProperty($expected, $value)
    {
        @$this->propertyIntegerDefinition->setMaxValue($value);
        $this->assertAttributeSame($expected, 'maxValue', $this->propertyIntegerDefinition);
    }

    /**
     * @depends testSetMaxValueCastsValueToIntegerAndSetsProperty
     */
    public function testGetMaxValueReturnsPropertyValue()
    {
        $this->propertyIntegerDefinition->setMaxValue(100);
        $this->assertSame(100, $this->propertyIntegerDefinition->getMaxValue());
    }

    /**
     * @dataProvider integerCastDataProvider
     * @param integer $expected
     * @param mixed $value
     */
    public function testSetMinValueCastsValueToIntegerAndSetsProperty($expected, $value)
    {
        @$this->propertyIntegerDefinition->setMinValue($value);
        $this->assertAttributeSame($expected, 'minValue', $this->propertyIntegerDefinition);
    }

    /**
     * @depends testSetMinValueCastsValueToIntegerAndSetsProperty
     */
    public function testGetMinValueReturnsPropertyValue()
    {
        $this->propertyIntegerDefinition->setMinValue(100);
        $this->assertSame(100, $this->propertyIntegerDefinition->getMinValue());
    }
}
