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

use Dkd\PhpCmis\DataObjects\PropertyDecimalDefinition;
use Dkd\PhpCmis\Enum\DecimalPrecision;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyDecimalDefinitionTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyDecimalDefinition
     */
    protected $propertyDecimalDefinition;

    public function setUp()
    {
        $this->propertyDecimalDefinition = new PropertyDecimalDefinition('testId');
    }

    public function testAssertIsInstanceOfAbstractPropertyDefinition()
    {
        $this->assertInstanceOf(
            '\\Dkd\\PhpCmis\\DataObjects\\AbstractPropertyDefinition',
            $this->propertyDecimalDefinition
        );
    }

    /**
     * @dataProvider integerCastDataProvider
     * @param integer $expected
     * @param mixed $value
     */
    public function testSetMaxValueCastsValueToIntegerAndSetsProperty($expected, $value)
    {
        @$this->propertyDecimalDefinition->setMaxValue($value);
        $this->assertAttributeSame($expected, 'maxValue', $this->propertyDecimalDefinition);
    }

    /**
     * @depends testSetMaxValueCastsValueToIntegerAndSetsProperty
     */
    public function testGetMaxValueReturnsPropertyValue()
    {
        $this->propertyDecimalDefinition->setMaxValue(100);
        $this->assertSame(100, $this->propertyDecimalDefinition->getMaxValue());
    }

    /**
     * @dataProvider integerCastDataProvider
     * @param integer $expected
     * @param mixed $value
     */
    public function testSetMinValueCastsValueToIntegerAndSetsProperty($expected, $value)
    {
        @$this->propertyDecimalDefinition->setMinValue($value);
        $this->assertAttributeSame($expected, 'minValue', $this->propertyDecimalDefinition);
    }

    /**
     * @depends testSetMinValueCastsValueToIntegerAndSetsProperty
     */
    public function testGetMinValueReturnsPropertyValue()
    {
        $this->propertyDecimalDefinition->setMinValue(100);
        $this->assertSame(100, $this->propertyDecimalDefinition->getMinValue());
    }

    public function testSetPrecisionSetsProperty()
    {
        $precision = DecimalPrecision::cast(DecimalPrecision::BITS32);
        $this->propertyDecimalDefinition->setPrecision($precision);
        $this->assertAttributeSame($precision, 'precision', $this->propertyDecimalDefinition);
    }

    /**
     * @depends testSetPrecisionSetsProperty
     */
    public function testGetPrecisionReturnsPropertyValue()
    {
        $precision = DecimalPrecision::cast(DecimalPrecision::BITS32);
        $this->propertyDecimalDefinition->setPrecision($precision);
        $this->assertSame($precision, $this->propertyDecimalDefinition->getPrecision());
    }
}
