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

use Dkd\PhpCmis\DataObjects\PropertyInteger;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyIntegerTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyInteger
     */
    protected $propertyInteger;

    public function setUp()
    {
        $this->propertyInteger = new PropertyInteger();
    }

    public function testSetValuesSetsProperty()
    {
        $values = array(2, 5);
        $this->propertyInteger->setValues($values);
        $this->assertAttributeSame($values, 'values', $this->propertyInteger);
    }

    /**
     * @dataProvider IntegerCastDataProvider
     */
    public function testSetValuesThrowsExceptionIfInvalidValuesGiven($expected, $value)
    {
        // use all non integer values
        if (!is_integer($value)) {
            $this->setExpectedException('\\InvalidArgumentException', null, 1413440336);
            $this->propertyInteger->setValues(array($value));
        }
    }

    public function testSetValueSetsValuesProperty()
    {
        $this->propertyInteger->setValue(2);
        $this->assertAttributeSame(array(2), 'values', $this->propertyInteger);
    }

    /**
     * @dataProvider IntegerCastDataProvider
     */
    public function testSetValueThrowsExceptionIfInvalidValueGiven($expected, $value)
    {
        // use all non integer values
        if (!is_integer($value)) {
            $this->setExpectedException('\\InvalidArgumentException', null, 1413440336);
            $this->propertyInteger->setValue(array($value));
        }
    }
}
